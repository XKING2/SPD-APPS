<?php

namespace App\Http\Controllers;

use App\Imports\OrbQuestionImport;
use App\Models\Desas;
use App\Models\FuzzyRule;
use App\Models\FuzzyScore;
use App\Models\OrbOption;
use App\Models\OrbQuest;
use App\Models\OrbResult;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

   


    public function shownilaiobservasi(Request $request, string $seleksiHash, string $desaHash)
    {
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $desaId    = Hashids::decode($desaHash)[0] ?? null;

        if (!$seleksiId || !$desaId) {
            abort(404);
        }

        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        $desa = Desas::findOrFail($desaId);

        // 🔍 Ambil hasil ujian (bukan user)
        $results = ResultExam::with('user')
            ->where('id_seleksi', $seleksi->id)
            ->where('type', 'ORB') // ✅ hanya ORB
            ->whereHas('user', function ($q) use ($desaId, $request) {
                $q->where('role', 'users')
                ->where('id_desas', $desaId)
                ->when($request->search, function ($qq) use ($request) {
                    $qq->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('score')
            ->get();

        return view('penguji.nilai.nilaiobservasi', compact(
            'results',
            'desa',
            'seleksi'
        ))->with([
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desa->id),
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
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            if ($request->hasFile('zip')) {
                $zip = new \ZipArchive;
                if ($zip->open($request->file('zip')->getRealPath()) === true) {
                    $zip->extractTo($tempDir);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuka file ZIP');
                }
            }

            // ⬇️ IMPORT
            Excel::import(
                new OrbQuestionImport($tempDir),
                $request->file('excel')
            );

            $this->deleteDirectory($tempDir);

            // 🔐 LOG AKTIVITAS (IMPORT)
            activity_log(
                'import',
                'Import soal ORB melalui Excel',
                null,
                null,
                [
                    'file' => $request->file('excel')->getClientOriginalName(),
                    'with_images' => $request->hasFile('zip')
                ]
            );

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

    public function create()
    {
        return view('penguji.tambahsoal.tambahorb'); // sesuaikan path blade
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'                    => 'required|string|max:100',
            'subject_penilaian'          => 'required|string|max:100',
            'pertanyaan'                 => 'required|string',
            'image'                      => 'nullable|image|max:2048',

            'options'                    => 'required|array|size:5',
            'options.*.label'            => 'required|in:A,B,C,D,E',
            'options.*.opsi_tulisan'     => 'required|string',
            'options.*.point'            => 'required|integer|between:1,5',
        ]);



        DB::beginTransaction();

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('Observasi/' . strtolower($request->subject), 'public');
            }

            $question = OrbQuest::create([
                'subject'    => $request->subject,
                'subject_penilaian' => $request->subject_penilaian,
                'pertanyaan' => $request->pertanyaan,
                'image_path' => $imagePath,
            ]);

            foreach ($request->options as $opt) {
                OrbOption::create([
                    'id_orb'       => $question->id,
                    'label'        => $opt['label'],
                    'opsi_tulisan' => $opt['opsi_tulisan'],
                    'point'        => $opt['point'],
                ]);
            }

            DB::commit();

            // 🔐 LOG AKTIVITAS (AMAN)
            activity_log(
                'Store',
                'Menambah Data Soal Orb',
                $question,
                null,
                collect($question)->toArray()
            );

            return redirect()
                ->route('addorb')
                ->with('success', 'Soal Observasi berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan soal: ' . $e->getMessage()
            ]);
        }
    }

    public function editobservasi(string $hashWWN)
    {

        $decoded = Hashids::decode($hashWWN);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $question = OrbQuest::with('options')->findOrFail($id);

        $labels = ['A','B','C','D','E'];

        foreach ($labels as $label) {
            if (!$question->options->firstWhere('label', $label)) {
                $question->options->push(
                    new OrbOption([
                        'label' => $label,
                        'opsi_tulisan' => '',
                        'point' => 1,
                    ])
                );
            }
        }

        return view('penguji.updatesoal.updateORB', [
            'question'    => $question,
           
        ]);

    }

    public function updateobservasi(Request $request, $id)
    {
        $question = OrbQuest::findOrFail($id);

        $request->validate([
            'subject'      => 'required|string',
            'pertanyaan'   => 'required|string',
            'image'        => 'nullable|image|max:2048',

            'options'              => 'required|array|size:5',
            'options.*.id'         => 'nullable|exists:orb_options,id',
            'options.*.label'      => 'required|in:A,B,C,D,E',
            'options.*.opsi'       => 'required|string',
            'options.*.point'      => 'required|integer|min:1|max:5',
        ]);

        
        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('Observasi/' . strtolower($request->subject), 'public');
        }

        
        $question->update([
            'subject'     => $request->subject,
            'pertanyaan'  => $request->pertanyaan,
            'image_path'  => $imagePath,
        ]);

        
        foreach ($request->options as $opt) {
            OrbOption::updateOrCreate(
                [
                    'id' => $opt['id'] ?? null,
                ],
                [
                    'id_orb'      => $question->id,
                    'label'        => $opt['label'],
                    'opsi_tulisan' => $opt['opsi'],
                    'point'        => $opt['point'],
                ]
            );
        }

        // 🔐 LOG AKTIVITAS (AMAN)
            activity_log(
                'Update',
                'Mengubah Data Soal Orb',
                $question,
                null,
                collect($question)->toArray()
            );

        

        return redirect()
            ->route('addorb')
            ->with('success', 'Soal wawancara berhasil diperbarui');
    }

    public function multiDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*'=> 'exists:orb_questions,id'
        ]);

        // 🔁 SIMPAN DATA SEBELUM DIHAPUS (RINGKAS)
        $count = OrbQuest::whereIn('id', $request->ids)->count();

        DB::transaction(function () use ($request) {
            OrbQuest::whereIn('id', $request->ids)->delete();
        });

        // 🔐 LOG AKTIVITAS (BULK DELETE)
        activity_log(
            'delete',
            'Menghapus beberapa soal ORB',
            null,
            [
                'total_deleted' => $count,
                'ids' => $request->ids
            ],
            null
        );

        return back()->with('success', 'Soal terpilih berhasil dihapus');
    }
}
