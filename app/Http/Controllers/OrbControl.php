<?php

namespace App\Http\Controllers;

use App\Imports\OrbQuestionImport;
use App\Models\Desas;
use App\Models\FuzzyRule;
use App\Models\FuzzyScore;
use App\Models\OrbQuest;
use App\Models\OrbResult;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class OrbControl extends Controller
{
    public function addnilaiorb(string $seleksiHash, string $userHash)
    {
        // Decode hash → ID
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $userId    = Hashids::decode($userHash)[0] ?? null;

        if (!$seleksiId || !$userId) {
            abort(404);
        }

        $user = User::with('desas')->findOrFail($userId);
        $seleksi = seleksi::findOrFail($seleksiId);
        $desaHash = Hashids::encode($user->id_desas);

        return view('penguji.tambahform.tambahnilaiorb', [
            'user'         => $user,
            'seleksi'      => $seleksi,
            'seleksiHash'  => $seleksiHash,
            'userHash'     => $userHash,
            'desaHash'    => $desaHash,
        ]);
    }

    public function storeOrb(Request $request, string $seleksiHash, string $userHash)
    {

        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $userId    = Hashids::decode($userHash)[0] ?? null;

        if (!$seleksiId || !$userId) {
            abort(404);
        }

        $seleksi = Seleksi::findOrFail($seleksiId);
        $user    = User::findOrFail($userId);

        $validated = $request->validate([
            'kerapian'    => 'required|integer|min:0|max:100',
            'kecepatan'   => 'required|integer|min:0|max:100',
            'ketepatan'   => 'required|integer|min:0|max:100',
            'efektifitas' => 'required|integer|min:0|max:100',
        ]);

        $totalScore = array_sum($validated);

        DB::transaction(function () use ($validated, $totalScore, $user, $seleksi) {

            /** ================= ORB RESULT ================= */
            OrbResult::create(array_merge($validated, [
                'user_id'    => $user->id,
            ]));

            /** ================= RESULT EXAM ================= */
            ResultExam::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'type'       => 'ORB',
                ],
                [
                    'score'        => $totalScore,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ]
            );

            /** ================= FUZZY ================= */
            $fuzzyRule = FuzzyRule::where('min_value', '<=', $totalScore)
                ->where('max_value', '>=', $totalScore)
                ->firstOrFail();

            FuzzyScore::create([
                'user_id'        => $user->id,
                'id_seleksi'     => $seleksi->id,
                'type'           => 'ORB',
                'score_raw'      => $totalScore,
                'score_crisp'    => $fuzzyRule->crisp_value,
                'fuzzy_rule_id'  => $fuzzyRule->id,
            ]);
        });
        

        return redirect()->route(
            'showobservasi',
            [
                'seleksi' => $seleksi->id,
                'desa'    => $user->id_desas,
            ]
        )->with('success', 'Nilai observasi berhasil disimpan.');
    }

    public function shownilaiobservasi(Request $request, string $seleksiHash, string $desaHash)
    {
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $desaId    = Hashids::decode($desaHash)[0] ?? null;

        if (!$seleksiId || !$desaId) {
            abort(404);
        }

        $seleksi = Seleksi::findOrFail($seleksiId);
        $desa    = Desas::findOrFail($desaId);

        $seleksiHashForForm = Hashids::encode($seleksi->id);
        $desaHashForForm    = Hashids::encode($desa->id);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        // Ambil user berdasarkan desa
        $users = User::where('id_desas', $desaId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('penguji.nilai.nilaiobservasi', compact('users', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }

    public function showTambahORB()
    {
        // Ambil semua soal TPU (atau semua soal kalau mau)
        $questions = OrbQuest::latest()->get();

        return view('penguji.tambahsoalorbmain', [
            'questions' => $questions,
        ]);
    }

    public function storeORBs(Request $request)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls',
            'zip'   => 'nullable|file|mimes:zip',
        ]);

        $tempDir = storage_path('app/temp_images/' . uniqid());

        try {

            // Buat folder temp
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Extract ZIP jika ada
            if ($request->hasFile('zip')) {
                $zip = new \ZipArchive;
                if ($zip->open($request->file('zip')->getRealPath()) === true) {
                    $zip->extractTo($tempDir);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuka file ZIP');
                }
            }

            // Import Excel
            Excel::import(
                new OrbQuestionImport($tempDir),
                $request->file('excel')
            );

            // Bersihkan folder temp
            $this->deleteDirectory($tempDir);

            return back()->with('success', 'Soal Observasi berhasil diimport');

        } catch (\Throwable $e) {

            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }

            report($e);

            return back()->withErrors(
                'Gagal import soal: ' . $e->getMessage()
            );
        }
    }


    protected function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) return;

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            is_dir($path)
                ? $this->deleteDirectory($path)
                : unlink($path);
        }

        rmdir($dir);
    }
}
