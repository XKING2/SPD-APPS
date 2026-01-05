<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestion;
use App\Models\exams;
use App\Models\ResultExam;
use App\Models\TpuOptionOrder;
use App\Models\wawancaraquest;
use App\Models\WWNOptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class Startcontrol extends Controller
{
        public function startTPU(string $exam)
    {
        // ================= DECODE HASH =================
        $decoded = Hashids::decode($exam);
        abort_if(empty($decoded), 404);

        $examId = $decoded[0];

        // ================= AMBIL EXAM =================
        $exam = exams::findOrFail($examId);

        // ================= SECURITY CHECK =================
        $user = Auth::user();

        if ($exam->id_desas !== $user->id_desas) {
            abort(403);
        }


        // ================= RESULT =================
        $result = ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $result) {
            return redirect()
                ->route('showmainujian')
                ->with('error', 'Silakan masukkan enrollment key terlebih dahulu');
        }

        // ================= QUESTIONS =================
        $questionOrder = $result->question_order;

        $questions = ExamQuestion::with('options')
            ->whereIn('id', $questionOrder)
            ->get()
            ->keyBy('id');

        $optionOrders = TpuOptionOrder::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('question_id');

        $sortedQuestions = collect($questionOrder)->map(function ($qid) use ($questions, $optionOrders) {
            $question = $questions[$qid];

            if (isset($optionOrders[$qid])) {
                $order = $optionOrders[$qid]->option_order;

                $question->setRelation(
                    'options',
                    collect($order)
                        ->map(fn ($oid) => $question->options->firstWhere('id', $oid))
                        ->filter()
                        ->values()
                );
            }

            return $question;
        })->values();

        return view('ujian.ujianTPUpage', [
            'exam'      => $exam,
            'questions' => $sortedQuestions,
            'result'    => $result,
        ]);
    }



    public function startWWN(string $exam)
    {
        $user = Auth::user();
        // ================= DECODE HASH =================
        $decoded = Hashids::decode($exam);
        abort_if(empty($decoded), 404);

        $examId = $decoded[0];

        // ================= AMBIL EXAM =================
        $exam = Exams::findOrFail($examId);

        if ($exam->id_desas !== $user->id_desas) {
            abort(403);
        }

        $result = ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $questionOrder = $result->question_order;

        $questions = wawancaraquest::with('options')
            ->whereIn('id', $questionOrder)
            ->get()
            ->keyBy('id');

        $optionOrders = WWNOptionOrder::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('question_id');

        $sortedQuestions = collect($questionOrder)->map(function ($qid) use ($questions, $optionOrders) {

            if (!isset($questions[$qid])) {
                return null; // skip soal yang tidak ditemukan
            }

            $question = $questions[$qid];

            if (isset($optionOrders[$qid])) {
                $order = $optionOrders[$qid]->option_order;

                $question->setRelation(
                    'options',
                    collect($order)
                        ->map(fn ($oid) =>
                            $question->options->firstWhere('id', $oid)
                        )
                        ->filter()
                        ->values()
                );
            }

            return $question;
        })->filter()->values();

        return view('ujian.ujianWWNpage', [
            'exam'      => $exam,
            'questions' => $sortedQuestions,
            'result'    => $result,
        ]);

    }
}
