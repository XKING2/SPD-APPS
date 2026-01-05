<?php

namespace App\Http\Controllers;

use App\Imports\ExamQuestionImport;
use App\Models\Desas;
use App\Models\ExamQuestion;
use App\Models\exams;
use App\Models\Kecamatans;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;
use ZipArchive;

class tpuControl extends Controller
{
    public function storeTPU(Request $request, string $seleksiHash, string $desaHash)
    {
        // 1️⃣ Decode hash → ID
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $desaId    = Hashids::decode($desaHash)[0] ?? null;

        if (!$seleksiId || !$desaId) {
            abort(404);
        }

        // 2️⃣ Ambil model
        $seleksi = seleksi::findOrFail($seleksiId);
        $desa    = Desas::findOrFail($desaId);

        // 3️⃣ VALIDASI RELASI & AUTHORIZATION (WAJIB)
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }



        // 4️⃣ Validasi request
        $request->validate([
            'type'     => 'required|string',
            'duration' => 'required|integer|min:1',
            'excel'    => 'required|file|mimes:xlsx,xls',
            'zip'      => 'nullable|file|mimes:zip',
        ]);

        DB::beginTransaction();

        try {
            // 5️⃣ Buat exam
            $exam = exams::create([
                'id_seleksi' => $seleksi->id,
                'id_desas'   => $desa->id,
                'type'       => $request->type,
                'start_at'   => $request->start_at,
                'end_at'     => $request->end_at,
                'duration'   => $request->duration,
                'status'     => 'draft',
                'created_by'=> Auth::id(),
            ]);

            // 6️⃣ Prepare temp dir (AMAN)
            $tempDir = storage_path('app/temp_images/exam_' . uniqid());
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // 7️⃣ Extract ZIP jika ada
            if ($request->hasFile('zip')) {
                $zip = new \ZipArchive;
                if ($zip->open($request->file('zip')->getRealPath()) === true) {
                    $zip->extractTo($tempDir);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuka file ZIP');
                }
            }

            // 8️⃣ Import Excel
            Excel::import(
                new ExamQuestionImport($exam->id, $tempDir),
                $request->file('excel')
            );

            DB::commit();

            $this->deleteDirectory($tempDir);

            return back()->with('success', 'Soal TPU berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($tempDir) && is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }

            report($e);

            return back()->withErrors('Terjadi kesalahan saat menyimpan soal');
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

    public function editTPU($id)
    {
        $question = ExamQuestion::with('options')->findOrFail($id);

        return view('penguji.updatesoal.updateTPU', compact('question'));
    }

    public function updateTPU(Request $request, $id)
    {
        $question = ExamQuestion::findOrFail($id);

        $request->validate([
            'subject'        => 'required|string',
            'pertanyaan'     => 'required|string',
            'jawaban_benar'  => 'required|in:A,B,C,D',
            'image'          => 'nullable|image|max:2048',

            'options'                => 'required|array',
            'options.*.id'           => 'required|exists:wawancara_options,id',
            'options.*.text'         => 'required|string',
        ]);

        $imagePath = $question->image_name;

        if ($request->hasFile('image')) {

            if ($question->image_name && Storage::disk('public')->exists($question->image_name)) {
                Storage::disk('public')->delete($question->image_name);
            }

            $imagePath = $request->file('image')
                ->store('soal/' . strtolower($request->subject), 'public');
        }

        $question->update([
            'subject'       => $request->subject,
            'pertanyaan'    => $request->pertanyaan,
            'jawaban_benar' => $request->jawaban_benar,
            'image_name'    => $imagePath,
        ]);

        foreach ($request->options as $opt) {
            $question->options()
                ->where('id', $opt['id'])
                ->update([
                    'opsi_tulisan' => $opt['text'],
                ]);
        }

        return redirect()
            ->route('tambahtpu')
            ->with('success', 'Soal berhasil diperbarui');
    }

    public function shownilaiTPU(string $seleksiHash, string $desaHash)
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
                    ->where('fuzzy_scores.type', 'TPU');
            })
            ->select(
                'users.id',
                'users.name',
                'fuzzy_scores.score_raw'
            )
            ->orderBy('users.name')
            ->get();
        return view('penguji.nilai.nilaiTPU', compact(
            'users', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }

    public function showTambahTPU(string $seleksiHash, string $desaHash)
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

        // 🔒 VALIDASI RELASI
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }

        $exam = Exams::where('id_seleksi', $seleksi->id)
            ->where('id_desas', $desa->id)
            ->where('type', 'TPU')
            ->first();

        $questions = $exam
            ? ExamQuestion::where('id_exam', $exam->id)->latest()->get()
            : [];

        return view('penguji.tambahsoal.tambahTPU', compact(
            'seleksi', 'desa', 'exam', 'questions'
        ))->with([
            'types' => Exams::TYPES,
            'kecamatans' => Kecamatans::orderBy('nama_kecamatan')->get(),
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }

    

}
