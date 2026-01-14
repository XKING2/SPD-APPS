<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Desas;
use Illuminate\Http\Request;

class activitycontrol extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(20);

        return view('penguji.activityindex', compact('logs'));
    }

    
}
