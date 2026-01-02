<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ExamQuestionImport;
use App\Imports\WawancaraQuestionImport;
use App\Models\Desas;
use App\Models\ExamOption;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\ExamQuestion;
use App\Models\exams;
use App\Models\wawancaraoption;
use App\Models\wawancaraquest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamTPUanswer;
use App\Models\FuzzyRule;
use App\Models\FuzzyScore;
use App\Models\OrbResult;
use App\Models\PrakResult;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use App\Models\wawancaranswer;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class ujiancontrol extends Controller
{
    

    public function storeTPU(Request $request, Seleksi $seleksi, Desas $desa)
    {
        if ($seleksi->id_desas !== $desa->id) {
            abort(403);
        }

        $request->validate([
            'type'     => 'required|string',
            'duration' => 'required|integer|min:1',
            'excel'    => 'required|file|mimes:xlsx,xls',
            'zip'      => 'nullable|file|mimes:zip',
        ]);

        DB::beginTransaction();

        try {
            $exam = Exams::create([
                'id_seleksi' => $seleksi->id,
                'id_desas'   => $desa->id,
                'type'       => $request->type,
                'start_at'   => $request->start_at,
                'end_at'     => $request->end_at,
                'duration'   => $request->duration,
                'status'     => 'draft',
                'created_by'=> Auth::id(),
            ]);

            $tempDir = storage_path('app/temp_images/exam_' . uniqid());
            mkdir($tempDir, 0777, true);

            if ($request->hasFile('zip')) {
                $zip = new \ZipArchive;
                $zip->open($request->file('zip')->getRealPath());
                $zip->extractTo($tempDir);
                $zip->close();
            }

            Excel::import(
                new ExamQuestionImport($exam->id, $tempDir),
                $request->file('excel')
            );

            DB::commit();
            $this->deleteDirectory($tempDir);

            return back()->with('success', 'Soal TPU berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
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

    public function storeWawancara(Request $request, Seleksi $seleksi, Desas $desa)
    {
        Log::info('[STORE+IMPORT] Mulai proses create seleksi & exam');

        if ($seleksi->id_desas !== $desa->id) {
            abort(403);
        }

        $request->validate([
            'type'     => 'required|string',
            'duration' => 'required|integer|min:1',
            'excel'    => 'required|file|mimes:xlsx,xls',
            'zip'      => 'nullable|file|mimes:zip',
        ]);

        DB::beginTransaction();
       

        try {

            /** =========================
             * 2. BUAT EXAM WAWANCARA
             * ========================= */
            $exam = Exams::create([
                'id_seleksi' => $seleksi->id,
                'id_desas'   => $desa->id,
                'type'       => $request->type,
                'start_at'   => $request->start_at,
                'end_at'     => $request->end_at,
                'duration'   => $request->duration,
                'status'     => 'draft',
                'created_by'=> Auth::id(),
            ]);

            Log::info('[STORE+IMPORT] Exam dibuat', [
                'id_exam' => $exam->id
            ]);

            /** =========================
             * 3. SIAPKAN FOLDER TEMP
             * ========================= */
            $tempDir = storage_path('app/temp_wawancara/exam_' . uniqid());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            /** =========================
             * 4. EXTRACT ZIP (OPSIONAL)
             * ========================= */
            if ($request->hasFile('zip')) {
                $zip = new \ZipArchive;
                if ($zip->open($request->file('zip')->getRealPath()) === true) {
                    $zip->extractTo($tempDir);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal extract ZIP');
                }
            }

            /** =========================
             * 5. IMPORT SOAL (TERIKAT EXAM)
             * ========================= */
            Excel::import(
                new WawancaraQuestionImport(
                    $exam->id,
                    $tempDir
                ),
                $request->file('excel')
            );

            DB::commit();

            $this->deleteDirectory($tempDir);

            return back()->with('success', 'Soal TPU berhasil ditambahkan');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('[STORE+IMPORT] Gagal', [
                'message' => $e->getMessage(),
            ]);

            if (isset($tempDir)) {
                $this->deleteDirectory($tempDir);
            }

            return back()->withErrors([
                'import' => 'Proses gagal: ' . $e->getMessage()
            ]);
        }
    }


    public function editTPU($id)
    {
        $question = ExamQuestion::with('options')->findOrFail($id);

        return view('penguji.updatesoal.updateTPU', compact('question'));
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

        /** ======================
         * HANDLE IMAGE
         * ====================== */
        $imagePath = $question->image_name;

        if ($request->hasFile('image')) {

            // hapus gambar lama jika ada
            if ($question->image_name && Storage::disk('public')->exists($question->image_name)) {
                Storage::disk('public')->delete($question->image_name);
            }

            $imagePath = $request->file('image')
                ->store('soal/' . strtolower($request->subject), 'public');
        }

        /** ======================
         * UPDATE QUESTION
         * ====================== */
        $question->update([
            'subject'       => $request->subject,
            'pertanyaan'    => $request->pertanyaan,
            'jawaban_benar' => $request->jawaban_benar,
            'image_name'    => $imagePath,
        ]);

        /** ======================
         * UPDATE OPTIONS (PER ITEM)
         * ====================== */
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

    

    public function addnilaiprak($seleksiId, $user_id)
    {
        $user = User::with('desas')->findOrFail($user_id);

        return view(
            'penguji.tambahform.tambahnilaiprak',
            compact('user', 'seleksiId')
        );
    }

    public function storePrak(Request $request, $seleksiId, User $user)
    {
        // 🔒 Validasi seleksi memang milik desa user
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $user->id_desas)
            ->firstOrFail();

        // ✅ Validasi input
        $validated = $request->validate([
            'kerapian'    => 'required|integer|min:0|max:100',
            'kecepatan'   => 'required|integer|min:0|max:100',
            'ketepatan'   => 'required|integer|min:0|max:100',
            'efektifitas' => 'required|integer|min:0|max:100',
        ]);

        // ➕ Total skor
        $totalScore = array_sum($validated);

        DB::transaction(function () use (
            $validated,
            $totalScore,
            $user,
            $seleksi
        ) {

            // 📌 Simpan nilai praktik (per seleksi)
            PrakResult::updateOrCreate(
                [
                    'user_id'    => $user->id,
                ],
                $validated
            );

            // 📌 Simpan hasil ujian
            ResultExam::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'type'       => 'PRAK',
                ],
                [
                    'score'        => $totalScore,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ]
            );

            // 🧠 Cari fuzzy rule
            $fuzzyRule = FuzzyRule::where('min_value', '<=', $totalScore)
                ->where('max_value', '>=', $totalScore)
                ->firstOrFail();

            // 🧠 Simpan fuzzy score
            FuzzyScore::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'id_seleksi' => $seleksi->id,
                    'type'       => 'PRAK',
                ],
                [
                    'score_raw'     => $totalScore,
                    'score_crisp'   => $fuzzyRule->crisp_value,
                    'fuzzy_rule_id' => $fuzzyRule->id,
                ]
            );
        });

        // 🔁 Redirect BALIK ke list user desa + seleksi
        return redirect()->route(
            'showpraktik',
            [
                'seleksi' => $seleksi->id,
                'desa'    => $user->id_desas,
            ]
        )->with('success', 'Nilai praktik berhasil disimpan.');
    }



    public function addnilaiorb($seleksiId, $user_id)
    {
        $user = User::with('desas')->findOrFail($user_id);

        return view(
            'penguji.tambahform.tambahnilaiorb',
            compact('user', 'seleksiId')
        );
    }

    public function storeOrb(Request $request, $seleksiId, User $user)
{
    $seleksi = Seleksi::where('id', $seleksiId)
        ->where('id_desas', $user->id_desas)
        ->firstOrFail();

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


    

    public function startTPU(exams $exam)
    {
        // 🔒 SECURITY DESA
        if ($exam->id_desas !== Auth::user()->id_desas) {
            abort(403, 'Anda tidak berhak mengakses ujian ini');
        }

        $questions = $exam->questions()
            ->with(['options:id,id_Pertanyaan,label,opsi_tulisan'])
            ->select('id','pertanyaan','image_name')
            ->orderBy('id')
            ->get();
    

        return view('ujian.ujianTPUpage', [
            'exam'      => $exam,
            'questions' => $questions,
        ]);
    }

    public function startWWN(exams $exams)
    {
        // ================= SECURITY DESA =================
        if ($exams->id_desas !== Auth::user()->id_desas) {
            abort(403, 'Anda tidak berhak mengakses ujian ini');
        }

        // ================= AMBIL SOAL WAWANCARA =================
        $questions = $exams->wawancara()
            ->with([
                'options:id,id_wwn,label,opsi_tulisan,point'
            ])
            ->select('id', 'pertanyaan', 'image_path')
            ->orderBy('id')
            ->get();


        return view('ujian.ujianWWNpage', [
            'exams'      => $exams,
            'questions' => $questions,
        ]);
    }

    public function submit(Request $request, exams $exam)
    {
        Log::info('=== SUBMIT EXAM START ===', [
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
        ]);

        $user = Auth::user();

        if (!$request->has('answers') || !is_array($request->answers)) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid'
            ], 422);
        }

        // 🔒 Cegah submit ulang
        if (
            ResultExam::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->where('is_submitted', true)
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah disubmit'
            ], 409);
        }

        $answers = $request->answers;
        $score   = 0;

        try {

            DB::transaction(function () use ($answers, $user, $exam, &$score) {

                // ================== HITUNG NILAI ==================
                $questions = ExamQuestion::where('id_exam', $exam->id)
                    ->pluck('jawaban_benar', 'id');

                $optionLabels = ExamOption::whereIn('id', array_values($answers))
                    ->pluck('label', 'id');

                foreach ($answers as $questionId => $optionId) {

                    if (!isset($questions[$questionId])) {
                        continue;
                    }

                    $userLabel    = strtoupper($optionLabels[$optionId] ?? '');
                    $correctLabel = strtoupper($questions[$questionId]);

                    ExamTPUanswer::updateOrCreate(
                        [
                            'user_id'        => $user->id,
                            'exams_question' => $questionId,
                        ],
                        [
                            'exams_option' => $optionId,
                        ]
                    );

                    if ($userLabel === $correctLabel) {
                        $score += 10;
                    }
                }

                // ================== SIMPAN RESULT RAW ==================
                ResultExam::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'exam_id' => $exam->id,
                        'type'    => 'TPU',
                    ],
                    [
                        'score'        => $score,
                        'is_submitted' => true,
                        'submitted_at' => now(),
                    ]
                );

                // ================== FUZZY ==================
                $fuzzyRule = FuzzyRule::where('min_value', '<=', $score)
                    ->where('max_value', '>=', $score)
                    ->first();

                if (!$fuzzyRule) {
                    throw new \Exception("Fuzzy rule tidak ditemukan untuk nilai {$score}");
                }

                FuzzyScore::updateOrCreate(
                    [
                        'user_id'    => $user->id,
                        'id_seleksi' => $exam->id_seleksi, // 🔥 KUNCI UTAMA
                        'type'       => 'TPU',
                    ],
                    [
                        'score_raw'     => $score,
                        'score_crisp'   => $fuzzyRule->crisp_value,
                        'fuzzy_rule_id' => $fuzzyRule->id,
                    ]
                );
            });

        } catch (\Throwable $e) {

            Log::error('SUBMIT EXAM FAILED', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('showmainujian')
        ]);
    }




    public function submitWawancara(Request $request, exams $exams)
{
    $user = Auth::user();

    Log::info('=== SUBMIT WAWANCARA START ===', [
        'user_id' => $user->id,
        'exam_id' => $exams->id,
        'payload' => $request->all(),
    ]);

    // ================= VALIDASI DASAR =================
    if (!$request->has('answers') || !is_array($request->answers)) {
        return response()->json([
            'success' => false,
            'message' => 'Format jawaban tidak valid'
        ], 422);
    }

    // ================= CEGAH DOUBLE SUBMIT =================
    if (
        ResultExam::where('user_id', $user->id)
            ->where('exam_id', $exams->id)
            ->where('is_submitted', true)
            ->exists()
    ) {
        return response()->json([
            'success' => false,
            'message' => 'Wawancara sudah disubmit'
        ], 409);
    }

    $answers = $request->answers;
    $score   = 0;

    try {

        DB::transaction(function () use ($answers, $user, $exams, &$score) {

            Log::info('WAWANCARA TRANSACTION START');

            // ================= LOAD PERTANYAAN =================
            $questions = wawancaraquest::where('id_exams', $exams->id)
                ->pluck('id')
                ->toArray();

            if (empty($questions)) {
                throw new \Exception('Pertanyaan wawancara tidak ditemukan');
            }

            // ================= LOAD OPSI =================
            $options = wawancaraoption::whereIn('id', array_values($answers))
                ->get()
                ->keyBy('id');

            foreach ($answers as $questionId => $optionId) {

                if (!in_array($questionId, $questions)) {
                    continue;
                }

                if (!isset($options[$optionId])) {
                    continue;
                }

                $option = $options[$optionId];

                // ================= SIMPAN JAWABAN =================
                wawancaranswer::updateOrCreate(
                    [
                        'user_id'            => $user->id,
                        'wawancara_question' => $questionId,
                    ],
                    [
                        'wawancara_option' => $optionId,
                    ]
                );

                // ================= HITUNG NILAI =================
                $score += (int) $option->point;
            }

            // ================= LOCK NILAI RAW =================
            ResultExam::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'exam_id' => $exams->id,
                    'type'    => 'WWN',
                ],
                [
                    'score'        => $score,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ]
            );

            // ================= FUZZIFIKASI =================
            $fuzzyRule = FuzzyRule::where('min_value', '<=', $score)
                ->where('max_value', '>=', $score)
                ->first();

            if (!$fuzzyRule) {
                throw new \Exception("Fuzzy rule tidak ditemukan untuk nilai wawancara: {$score}");
            }

            FuzzyScore::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'id_seleksi' => $exams->id_seleksi, // 🔥 KUNCI UTAMA
                    'type'       => 'WWN',
                ],
                [
                    'score_raw'     => $score,
                    'score_crisp'   => $fuzzyRule->crisp_value,
                    'fuzzy_rule_id' => $fuzzyRule->id,
                ]
            );

            Log::info('WAWANCARA TRANSACTION COMPLETE', [
                'score_raw'   => $score,
                'score_crisp' => $fuzzyRule->crisp_value,
            ]);
        });

    } catch (\Throwable $e) {

        Log::error('SUBMIT WAWANCARA FAILED', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server'
        ], 500);
    }

    Log::info('=== SUBMIT WAWANCARA SUCCESS ===', [
        'user_id' => $user->id,
        'exam_id' => $exams->id,
        'score'   => $score
    ]);

    return response()->json([
        'success'  => true,
        'redirect' => route('showmainujian')
    ]);
}

public function validasiExam(Exams $exam, string $type)
{
    // 🔒 Proteksi status
    if ($exam->status !== 'draft') {
        return back()->withErrors([
            'status' => 'Exam sudah divalidasi sebelumnya'
        ]);
    }

    // 🔒 Validasi berdasarkan tipe ujian
    if ($type === 'TPU') {
        if ($exam->questions()->count() === 0) {
            return back()->withErrors([
                'soal' => 'Tidak bisa validasi, soal TPU masih kosong'
            ]);
        }

        if ($exam->wawancara()->count() === 0) {
            return back()->withErrors([
                'soal' => 'Tidak bisa validasi, soal wawancara masih kosong'
            ]);
        }
    }

    if ($type === 'WWN') {
        if ($exam->wawancara()->count() === 0) {
            return back()->withErrors([
                'soal' => 'Tidak bisa validasi, soal wawancara masih kosong'
            ]);
        }
    }

    // ✅ Aktifkan exam
    $exam->update([
        'status' => 'active'
    ]);

    return back()->with('success', "Exam {$type} berhasil divalidasi dan diaktifkan");
}

public function verifyEnrollment(Request $request, exams $exam)
{
    $request->validate([
        'enrollment_key' => 'required|string'
    ]);

    // 🔒 Validasi keras
    if (
        $exam->enrollment_key !== strtoupper($request->enrollment_key) ||
        $exam->key_expired_at < now() ||
        $exam->status !== 'active'
    ) {
        return back()
            ->withErrors(['enrollment_key' => 'Enrollment key tidak valid atau sudah kedaluwarsa'])
            ->withInput();
    }

    // Simpan ke session (opsional tapi recommended)
    session([
        'exam_access_'.$exam->id => true
    ]);

    return redirect()->route('exam.tpu.start', $exam->id);
}

public function verifyEnrollments(Request $request, exams $exam)
{
    $request->validate([
        'enrollment_key' => 'required|string'
    ]);

    // 🔒 Validasi keras
    if (
        $exam->enrollment_key !== strtoupper($request->enrollment_key) ||
        $exam->key_expired_at < now() ||
        $exam->status !== 'active'
    ) {
        return back()
            ->withErrors(['enrollment_key' => 'Enrollment key tidak valid atau sudah kedaluwarsa'])
            ->withInput();
    }

    // Simpan ke session (opsional tapi recommended)
    session([
        'exam_access_'.$exam->id => true
    ]);

    return redirect()->route('exam.tpu.start', $exam->id);
}





}



