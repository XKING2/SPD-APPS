<?php

namespace App\Http\Controllers;

use App\Imports\WawancaraQuestionImport;
use App\Models\Desas;
use App\Models\exams;
use App\Models\Kecamatans;
use App\Models\seleksi;
use App\Models\User;
use App\Models\wawancaraoption;
use App\Models\wawancaraquest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class WWNControl extends Controller
{
   public function storeWawancara(Request $request)
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
                new WawancaraQuestionImport($tempDir),
                $request->file('excel')
            );

            // Bersihkan folder temp
            $this->deleteDirectory($tempDir);

            return back()->with('success', 'Soal TPU berhasil diimport');

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

    public function editWawancara($id)
    {
        $question = wawancaraquest::with('options')->findOrFail($id);

        // pastikan selalu 5 opsi (A–E)
        $labels = ['A','B','C','D','E'];

        foreach ($labels as $label) {
            if (!$question->options->firstWhere('label', $label)) {
                $question->options->push(
                    new wawancaraoption([
                        'label' => $label,
                        'opsi_tulisan' => '',
                        'point' => 1,
                    ])
                );
            }
        }

        return view('penguji.updatesoal.updateWWN', compact('question'));
    }

    public function updateWawancara(Request $request, $id)
    {
        $question = wawancaraquest::findOrFail($id);

        $request->validate([
            'subject'      => 'required|string',
            'pertanyaan'   => 'required|string',
            'image'        => 'nullable|image|max:2048',

            'options'              => 'required|array|size:5',
            'options.*.id'         => 'nullable|exists:wawancara_options,id',
            'options.*.label'      => 'required|in:A,B,C,D,E',
            'options.*.opsi'       => 'required|string',
            'options.*.point'      => 'required|integer|min:1|max:5',
        ]);

        
        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('wawancara/' . strtolower($request->subject), 'public');
        }

        
        $question->update([
            'subject'     => $request->subject,
            'pertanyaan'  => $request->pertanyaan,
            'image_path'  => $imagePath,
        ]);

        
        foreach ($request->options as $opt) {
            WawancaraOption::updateOrCreate(
                [
                    'id' => $opt['id'] ?? null,
                ],
                [
                    'id_wwn'      => $question->id,
                    'label'        => $opt['label'],
                    'opsi_tulisan' => $opt['opsi'],
                    'point'        => $opt['point'],
                ]
            );
        }

        return redirect()
            ->route('tambahwawan')
            ->with('success', 'Soal wawancara berhasil diperbarui');
    }

    public function shownilaiWWN(string $seleksiHash, string $desaHash)
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
        // Ambil desa
        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        $users = User::where('users.id_desas', $desaId)
            ->leftJoin('fuzzy_scores', function ($join) use ($seleksiId) {
                $join->on('users.id', '=', 'fuzzy_scores.user_id')
                    ->where('fuzzy_scores.id_seleksi', $seleksiId)
                    ->where('fuzzy_scores.type', 'WWN');
            })
            ->select(
                'users.id',
                'users.name',
                'fuzzy_scores.score_raw'
            )
            ->orderBy('users.name')
            ->get();

        return view('penguji.nilai.nilaiWWN', compact('users', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }

    public function showtambahwawancara()
    {

       // Ambil semua soal TPU (atau semua soal kalau mau)
        $questions = wawancaraquest::latest()->get();

        return view('penguji.tambahsoalWWNmain', [
            'questions' => $questions,
        ]);

    }
}
