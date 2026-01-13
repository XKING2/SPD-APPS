<?php

namespace App\Http\Controllers;

use App\Imports\WawancaraQuestionImport;
use App\Models\Desas;
use App\Models\exams;
use App\Models\Kecamatans;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use App\Models\wawancaraoption;
use App\Models\wawancaraquest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

            // 🔐 LOG AKTIVITAS (IMPORT)
            activity_log(
                'import',
                'Import soal WWN melalui Excel',
                null,
                null,
                [
                    'file' => $request->file('excel')->getClientOriginalName(),
                    'with_images' => $request->hasFile('zip')
                ]
            );

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

        public function create()
        {
            return view('penguji.tambahsoal.tambahWWN'); // sesuaikan path blade
        }

    public function store(Request $request)
    {
        $request->validate([
            'subject'        => 'required|string|max:100',
            'pertanyaan'     => 'required|string',
            'image'          => 'nullable|image|max:2048',

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
                    ->store('wawancara/' . strtolower($request->subject), 'public');
            }

            $question = wawancaraquest::create([
                'subject'    => $request->subject,
                'pertanyaan' => $request->pertanyaan,
                'image_path' => $imagePath,
            ]);

            foreach ($request->options as $opt) {
                wawancaraoption::create([
                    'id_wwn'       => $question->id,
                    'label'        => $opt['label'],
                    'opsi_tulisan' => $opt['opsi_tulisan'],
                    'point'        => $opt['point'],
                ]);
            }

            DB::commit();

            activity_log(
                'Create',
                'Menambah Data Soal WWN',
                $question,
                null,
                collect($question)->toArray()
            );

            return redirect()
                ->route('addWWN')
                ->with('success', 'Soal wawancara berhasil ditambahkan');

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


    public function editWawancara(string $hashWWN)
    {

        $decoded = Hashids::decode($hashWWN);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $question = wawancaraquest::with('options')->findOrFail($id);

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

        return view('penguji.updatesoal.updateWWN', [
            'question'    => $question,
           
        ]);

    }

    public function updateWawancara(Request $request, $id)
    {
        $question = wawancaraquest::findOrFail($id);

        $request->validate([
            'subject'      => 'required|string',
            'pertanyaan'   => 'required|string',
            'image'        => 'nullable|image|max:2048',

            'options'              => 'required|array|size:5',
            'options.*.id'         => 'nullable|exists:wwn_options,id',
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

        // 🔐 LOG AKTIVITAS (AMAN)
            activity_log(
                'Update',
                'Mengupdate Data Soal WWN',
                $question,
                null,
                collect($question)->toArray()
            );

        return redirect()
            ->route('addWWN')
            ->with('success', 'Soal wawancara berhasil diperbarui');
    }

    public function shownilaiWWN(Request $request, string $seleksiHash, string $desaHash)
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

        $results = ResultExam::with('user')
            ->where('id_seleksi', $seleksi->id)
            ->where('type', 'WWN') // ✅ hanya ORB
            ->whereHas('user', function ($q) use ($desaId, $request) {
                $q->where('role', 'users')
                ->where('id_desas', $desaId)
                ->when($request->search, function ($qq) use ($request) {
                    $qq->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('score')
            ->get();

        return view('penguji.nilai.nilaiWWN', compact('results', 'desa', 'seleksi'
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

    public function multiDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:wwn_options,id'
        ]);

        // 🔁 SIMPAN DATA SEBELUM DIHAPUS (RINGKAS)
        $count = wawancaraquest::whereIn('id', $request->ids)->count();

        DB::transaction(function () use ($request) {
            wawancaraquest::whereIn('id', $request->ids)->delete();
        });

        activity_log(
            'delete',
            'Menghapus beberapa soal Wawancara',
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
