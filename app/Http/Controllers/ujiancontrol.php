<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
use App\Models\ResultExam;
use App\Models\wawancaranswer;



class ujiancontrol extends Controller
{

    public function submit(Request $request, Exams $exam)
    {
        $user = Auth::user();

        if (!$request->has('answers') || !is_array($request->answers)) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid'
            ], 422);
        }

        $result = ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Attempt ujian tidak ditemukan'
            ], 403);
        }

        if ($result->is_submitted) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah disubmit'
            ], 409);
        }

        $answers = $request->answers;
        $score   = 0;

        try {

            DB::transaction(function () use ($answers, $user, $exam, $result, &$score) {

                $correctOptions = ExamQuestion::whereIn('id', $result->question_order)
                    ->pluck('correct_option_id', 'id'); 

                foreach ($answers as $questionId => $optionId) {

                    if (!isset($correctOptions[$questionId])) {
                        continue;
                    }

                    ExamTPUanswer::updateOrCreate(
                        [
                            'user_id'        => $user->id,
                            'exams_question' => $questionId,
                        ],
                        [
                            'exams_option' => $optionId,
                        ]
                    );
                    if ((int) $optionId === (int) $correctOptions[$questionId]) {
                        $score += 2.5;
                    }
                }

                $result->update([
                    'score'        => $score,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ]);

                $fuzzyRule = FuzzyRule::where('min_value', '<=', $score)
                    ->where('max_value', '>=', $score)
                    ->firstOrFail();
                    if (!$fuzzyRule) {
                    
                }


                FuzzyScore::updateOrCreate(
                    [
                        'user_id'    => $user->id,
                        'id_seleksi' => $exam->id_seleksi,
                        'type'       => $exam->type,
                    ],
                    [
                        'score_raw'     => $score,
                        'score_crisp'   => $fuzzyRule->crisp_value,
                        'fuzzy_rule_id' => $fuzzyRule->id,
                    ]
                );
            });

        } catch (\Throwable $e) {

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






    public function submitWawancara(Request $request, exams $exam)
    {
        $user = Auth::user();

        if (!$request->has('answers') || !is_array($request->answers)) {
            return response()->json([
                'success' => false,
                'message' => 'Format jawaban tidak valid'
            ], 422);
        }

        if (
            ResultExam::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
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

            DB::transaction(function () use ($answers, $user, $exam, &$score) {


                $questions = wawancaraquest::where('id_exams', $exam->id)
                    ->pluck('id')
                    ->toArray();

                if (empty($questions)) {
                    throw new \Exception('Pertanyaan wawancara tidak ditemukan');
                }

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

                    wawancaranswer::updateOrCreate(
                        [
                            'user_id'            => $user->id,
                            'wawancara_question' => $questionId,
                        ],
                        [
                            'wawancara_option' => $optionId,
                        ]
                    );

                    $score += (int) $option->point;
                }


                ResultExam::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'exam_id' => $exam->id,
                        'type'    => 'WWN',
                    ],
                    [
                        'score'        => $score,
                        'is_submitted' => true,
                        'submitted_at' => now(),
                    ]
                );

                $fuzzyRule = FuzzyRule::where('min_value', '<=', $score)
                    ->where('max_value', '>=', $score)
                    ->first();

                if (!$fuzzyRule) {
                    throw new \Exception("Fuzzy rule tidak ditemukan untuk nilai wawancara: {$score}");
                }

                FuzzyScore::updateOrCreate(
                    [
                        'user_id'    => $user->id,
                        'id_seleksi' => $exam->id_seleksi, 
                        'type'       => 'WWN',
                    ],
                    [
                        'score_raw'     => $score,
                        'score_crisp'   => $fuzzyRule->crisp_value,
                        'fuzzy_rule_id' => $fuzzyRule->id,
                    ]
                );

            });

        } catch (\Throwable $e) {

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


    public function validasiExam(Exams $exam, string $type)
    {
        if ($exam->status !== 'draft') {
            return back()->withErrors([
                'status' => 'Exam sudah divalidasi sebelumnya'
            ]);
        }

        if ($type === 'TPU') {
            if ($exam->questions()->count() === 0) {
                return back()->withErrors([
                    'soal' => 'Tidak bisa validasi, soal TPU masih kosong'
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

        $exam->update([
            'status' => 'active'
        ]);

        return back()->with('success', "Exam {$type} berhasil divalidasi dan diaktifkan");
    }



}



