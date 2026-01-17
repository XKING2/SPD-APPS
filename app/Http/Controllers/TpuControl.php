<?php

namespace App\Http\Controllers;


use App\Imports\ExamQuestionImport;
use App\Models\Desas;
use App\Models\ExamOption;
use App\Models\ExamQuestion;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;
use ZipArchive;

class TpuControl extends Controller
{
        public function storeTPU(Request $request)
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
                new ExamQuestionImport($tempDir),
                $request->file('excel')
            );

            // Bersihkan folder temp
            $this->deleteDirectory($tempDir);

            // 🔐 LOG AKTIVITAS (IMPORT)
            activity_log(
                'import',
                'Import soal TPU melalui Excel',
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
        return view('penguji.tambahsoal.tambahTPU'); // sesuaikan path blade
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'        => 'required|string|max:100',
            'pertanyaan'     => 'required|string',
            'jawaban_benar'  => 'required|in:A,B,C,D',
            'image'          => 'nullable|image|max:2048',

            'options'                => 'required|array|size:4',
            'options.*.label'        => 'required|in:A,B,C,D',
            'options.*.text'         => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            /** =========================
             * Upload image (optional)
             * ========================= */
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('soal/' . strtolower($request->subject), 'public');
            }

            /** =========================
             * Create question
             * ========================= */
            $question = ExamQuestion::create([
                'subject'     => $request->subject,
                'pertanyaan'  => $request->pertanyaan,
                'image_name'  => $imagePath,
            ]);

            /** =========================
             * Create options
             * ========================= */
            $correctOptionId = null;

            foreach ($request->options as $opt) {
                $option = ExamOption::create([
                    'id_Pertanyaan' => $question->id,
                    'label'         => $opt['label'],       // A/B/C/D
                    'opsi_tulisan'  => $opt['text'],
                ]);

                // Cocokkan jawaban benar
                if ($opt['label'] === $request->jawaban_benar) {
                    $correctOptionId = $option->id;
                }
            }

            /** =========================
             * Safety check
             * ========================= */
            if (!$correctOptionId) {
                throw new \Exception('Jawaban benar tidak valid');
            }

            /** =========================
             * Update correct option
             * ========================= */
            $question->update([
                'correct_option_id' => $correctOptionId,
            ]);

            DB::commit();

             // 🔐 LOG AKTIVITAS (AMAN)
            activity_log(
                'Create',
                'Membuat Data Soal TPU',
                $question,
                null,
                collect($question)->toArray()
            );

            return redirect()
                ->route('addTPU')
                ->with('success', 'Soal berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();

            // hapus image jika gagal
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editTPU(string $hashTPU)
    {
        $decoded = Hashids::decode($hashTPU);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $question = ExamQuestion::with('options')->findOrFail($id);

        return view('penguji.updatesoal.updateTPU', [
            'question'    => $question,
           
        ]);
    }

    public function updateTPU(Request $request, $id)
    {
        $question = ExamQuestion::with('options')->findOrFail($id);

        $request->validate([
            'subject'        => 'required|string',
            'pertanyaan'     => 'required|string',
            'jawaban_benar'  => 'required|in:A,B,C,D',
            'image'          => 'nullable|image|max:2048',

            'options'                => 'required|array',
            'options.*.id'           => 'required|exists:tpu_options,id',
            'options.*.text'         => 'required|string',
        ]);

        /** ================================
         * Mapping A/B/C/D → option ID
         * ================================ */
        $indexMap = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
        ];

        $options = array_values($request->options);

        if (!isset($options[$indexMap[$request->jawaban_benar]])) {
            return back()->withErrors([
                'jawaban_benar' => 'Jawaban benar tidak valid'
            ]);
        }

        $correctOptionId = $options[
            $indexMap[$request->jawaban_benar]
        ]['id'];

        /** ================================
         * Image handling
         * ================================ */
        $imagePath = $question->image_name;

        if ($request->hasFile('image')) {

            if ($question->image_name &&
                Storage::disk('public')->exists($question->image_name)) {
                Storage::disk('public')->delete($question->image_name);
            }

            $imagePath = $request->file('image')
                ->store('soal/' . strtolower($request->subject), 'public');
        }

        /** ================================
         * Update question
         * ================================ */
        $question->update([
            'subject'            => $request->subject,
            'pertanyaan'         => $request->pertanyaan,
            'image_name'         => $imagePath,
            'correct_option_id'  => $correctOptionId,
        ]);

        /** ================================
         * Update options
         * ================================ */
        foreach ($request->options as $opt) {
            $question->options()
                ->where('id', $opt['id'])
                ->update([
                    'opsi_tulisan' => $opt['text'],
                ]);
        }

         // 🔐 LOG AKTIVITAS (AMAN)
            activity_log(
                'Update',
                'Mengupdate Data Soal TPU',
                $question,
                null,
                collect($question)->toArray()
            );

        return redirect()
            ->route('addTPU')
            ->with('success', 'Soal berhasil diperbarui');
    }

    public function shownilaiTPU(Request $request, string $seleksiHash, string $desaHash)
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
            ->where('type', 'TPU') // ✅ hanya ORB
            ->whereHas('user', function ($q) use ($desaId, $request) {
                $q->where('role', 'users')
                ->where('id_desas', $desaId)
                ->when($request->search, function ($qq) use ($request) {
                    $qq->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('score')
            ->get();

        return view('penguji.nilai.nilaiTPU', compact(
            'results', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }

    public function showTambahTPU()
    {
        // Ambil semua soal TPU (atau semua soal kalau mau)
        $questions = ExamQuestion::latest()->get();

        return view('penguji.tambahsoalTPUmain', [
            'questions' => $questions,
        ]);
    }


    
    public function multiDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tpu_questions,id'
        ]);

        // 🔁 SIMPAN DATA SEBELUM DIHAPUS (RINGKAS)
        $count = ExamQuestion::whereIn('id', $request->ids)->count();

        DB::transaction(function () use ($request) {
            ExamQuestion::whereIn('id', $request->ids)->delete();
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
