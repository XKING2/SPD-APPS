<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestion;
use App\Models\exams;
use App\Models\ResultExam;
use App\Models\TpuOptionOrder;
use App\Models\wawancaraquest;
use App\Models\WWNOptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class Enrollcontrol extends Controller
{
    public function verifyEnrollmentTPU(Request $request, exams $exam)
    {
        $request->validate([
            'enrollment_key' => 'required|string'
        ]);

        $user = Auth::user();

        // ================= VALIDASI EXAM =================
        if (
            $exam->type !== 'tpu' ||
            $exam->enrollment_key !== strtoupper($request->enrollment_key) ||
            $exam->key_expired_at < now() ||
            $exam->status !== 'active'
        ) {



            return back()->withErrors([
                'enrollment_key' => 'Enrollment key tidak valid atau ujian tidak aktif'
            ]);


        }

        // ================= CEK ATTEMPT =================
        $result = ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($result && $result->is_submitted) {
            return redirect()
                ->route('showmainujian')
                ->with('error', 'Ujian TPU sudah pernah diselesaikan');
        }

        // ================= BUAT ATTEMPT BARU =================
        if (!$result) {

            $questionIds = ExamQuestion::where('id_exam', $exam->id)
                ->pluck('id')
                ->toArray();

            if (count($questionIds) === 0) {
                return back()->withErrors([
                    'enrollment_key' => 'Soal TPU belum tersedia'
                ]);
            }

            $shuffledQuestions = Arr::shuffle($questionIds);

            $result = ResultExam::create([
                'exam_id'        => $exam->id,
                'user_id'        => $user->id,
                'type'           => 'TPU',
                'question_order' => $shuffledQuestions,
                'started_at'     => now(),
                'is_submitted'   => false,
            ]);

            // ================= RANDOM OPTION TPU =================
            $questions = ExamQuestion::with('options')
                ->whereIn('id', $shuffledQuestions)
                ->get();

            foreach ($questions as $question) {

                $optionOrder = $question->options
                    ->pluck('id')
                    ->shuffle()
                    ->values()
                    ->toArray();

                TpuOptionOrder::create([
                    'exam_id'      => $exam->id,
                    'user_id'      => $user->id,
                    'question_id'  => $question->id,
                    'option_order' => $optionOrder,
                ]);
            }

        }

        session([
            'exam_access_'.$exam->id => true,
            'result_exam_id'         => $result->id
        ]);


        return redirect()->route(
            'exam.tpu.start',
            Hashids::encode($exam->id)
        );
    }


    
    public function verifyEnrollmentWawancara(Request $request, Exams $exam)
    {
        $request->validate([
            'enrollment_key' => 'required|string'
        ]);

        $user = Auth::user();


        // ================= VALIDASI EXAM =================
        if (
            $exam->type !== 'wwn' ||
            $exam->enrollment_key !== strtoupper($request->enrollment_key) ||
            $exam->key_expired_at < now() ||
            $exam->status !== 'active'
        ) {


            return back()->withErrors([
                'enrollment_key' => 'Enrollment key tidak valid atau ujian tidak aktif'
            ]);
        }

        // ================= CEK ATTEMPT =================
        $result = ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($result && $result->is_submitted) {
            return redirect()
                ->route('showmainujian')
                ->with('error', 'Ujian wawancara sudah pernah diselesaikan');
        }

        // ================= BUAT ATTEMPT BARU =================
        if (!$result) {

            // ================= AMBIL SOAL =================
            $questionIds = wawancaraquest::where('id_exams', $exam->id)
                ->pluck('id')
                ->toArray();

            if (count($questionIds) === 0) {
                return back()->withErrors([
                    'enrollment_key' => 'Soal wawancara belum tersedia'
                ]);
            }

            $shuffledQuestions = Arr::shuffle($questionIds);

            $result = ResultExam::create([
                'exam_id'        => $exam->id,
                'user_id'        => $user->id,
                'type'           => 'WWN',
                'question_order' => $shuffledQuestions,
                'started_at'     => now(),
                'is_submitted'   => false,
            ]);

            // ================= RANDOM OPTION =================
            $questions = wawancaraquest::with('options')
                ->whereIn('id', $shuffledQuestions)
                ->get();

            foreach ($questions as $question) {

                $optionOrder = $question->options
                    ->pluck('id')
                    ->shuffle()
                    ->values()
                    ->toArray();

                WWNOptionOrder::create([
                    'exam_id'      => $exam->id,
                    'user_id'      => $user->id,
                    'question_id'  => $question->id,
                    'option_order' => $optionOrder,
                ]);
            }
        }

        session([
            'exam_access_'.$exam->id => true,
            'result_exam_id'         => $result->id
        ]);

        return redirect()->route(
            'exam.wwn.start',
            Hashids::encode($exam->id)
        );

    }
}
