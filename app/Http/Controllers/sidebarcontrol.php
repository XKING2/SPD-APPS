<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\biodata;
use App\Models\exams;
use App\Models\ResultExam;

class sidebarcontrol extends Controller
{
    public function showdashboard()
    {
        $user = Auth::user();

        // Ambil biodata berdasarkan user login
        $biodata = Biodata::where('id_user', Auth::id())->first();
        $profileImg = $biodata->profile_img ?? 'img/undraw_profile.svg';




        return view('users.dashboard', compact(
            'user',
            'biodata',
            'profileImg'
        ));
    }
    public function showbiodata()
    {
        $biodata = Biodata::where('id_user', Auth::id())->first();

        return view('users.biodata', compact('biodata'));
    }

    public function showverikasibio()
    {
        $biodata = Biodata::where('id_user', Auth::id())->first();

        return view('users.verivikasi', compact('biodata'));
    }

    public function showcekdata()
    {
        return view('users.cekdata');
    }

    public function showmainujian()
    {
        $user = Auth::user();
        $biodata = Biodata::where('id_user', $user->id)->first();

        $examTPU = exams::where('id_desas', $user->id_desas)
            ->where('type', 'tpu')
            ->where('status', 'active')
            ->first();

        
        $examWWN = exams::where('id_desas', $user->id_desas)
            ->where('type', 'wwn')
            ->where('status', 'active')
            ->first();

        $profileImg = $biodata->profile_img ?? 'img/undraw_profile.svg';

        $ExamResultTPU = $this->getExamResult($examTPU, $user->id);
        $ExamResultWWN = $this->getExamResult($examWWN, $user->id);

        return view('ujian.mainujian', compact(
            'user',
            'biodata',
            'profileImg',
            'examTPU',
            'examWWN',
            'ExamResultTPU',
            'ExamResultWWN'
        ));
    }

    private function getExamResult(?Exams $exam, int $userId): ?ResultExam
    {
        if (!$exam) {
            return null;
        }

        return ResultExam::where('exam_id', $exam->id)
            ->where('user_id', $userId)
            ->first();
    }





   




}
