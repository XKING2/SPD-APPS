<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\biodata;
use App\Models\exams;
use App\Models\Formasi;
use App\Models\ResultExam;

class sidebarcontrol extends Controller
{
    public function showdashboard()
    {
        $user = Auth::user();

        // Ambil biodata berdasarkan user login
        $biodata = Biodata::where('id_user', Auth::id())->first();
        $profileImg = $biodata->profile_img ?? 'img/undraw_profile.svg';

        // Ambil ujian TPU
        $examTPU = exams::where('type', 'tpu')
            ->where('id_desas', $user->id_desas) // Filter by user's desa
            ->where('status', 'active')
            ->first();
        
        // Ambil ujian Wawancara
        $examWawancara = exams::where('type', 'wwn')
            ->where('id_desas', $user->id_desas) // Filter by user's desa
            ->where('status', 'active')
            ->first();

        // Ambil ujian Observasi
        $examORB = exams::where('type', 'orb')
            ->where('id_desas', $user->id_desas) // Filter by user's desa
            ->where('status', 'active') 
            ->first();

        // Ambil hasil ujian TPU
        $ExamResultTPU = ResultExam::where('user_id', $user->id)
            ->where('exam_id', $examTPU?->id)
            ->first();

        // Ambil hasil ujian Wawancara
        $ExamResultWWN = ResultExam::where('user_id', $user->id)
            ->where('exam_id', $examWawancara?->id)
            ->first();

        // Ambil hasil ujian Observasi
        $ExamResultORB = ResultExam::where('user_id', $user->id)
            ->where('exam_id', $examORB?->id)
            ->first();

        return view('users.dashboard', compact(
            'user',
            'biodata',
            'profileImg',
            'examTPU',
            'examWawancara',
            'examORB',
            'ExamResultTPU',
            'ExamResultWWN',
            'ExamResultORB'
        ));
    }
    
    public function showbiodata()
    {
        $user = Auth::user(); // OBJECT

        $formasis = Formasi::with('kebutuhan')
            ->where('id_desas', $user->id_desas)
            ->where('tahun', now()->year)
            ->get();

        $biodata = Biodata::where('id_user', $user->id)->first();

        return view('users.biodata', compact('biodata', 'user', 'formasis'));
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

        $examORB = exams::where('id_desas', $user->id_desas)
            ->where('type', 'orb')
            ->where('status', 'active')
            ->first();

        $profileImg = $biodata->profile_img ?? 'img/undraw_profile.svg';

        $ExamResultTPU = $this->getExamResult($examTPU, $user->id);
        $ExamResultWWN = $this->getExamResult($examWWN, $user->id);
        $ExamResultORB = $this->getExamResult($examORB, $user->id);

        return view('ujian.mainujian', compact(
            'user',
            'biodata',
            'profileImg',
            'examTPU',
            'examWWN',
            'examORB',
            'ExamResultTPU',
            'ExamResultWWN',
            'ExamResultORB'
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
