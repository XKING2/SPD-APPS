<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        string $action,
        string $description,
        $subject = null,
        array $oldValues = null,
        array $newValues = null,
        Request $request = null
    ): void {
        $user = Auth::user();
        $request ??= request();

        ActivityLog::create([
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'action'      => $action,
            'description' => $description,
            'subject_type'=> $subject ? get_class($subject) : null,
            'subject_id'  => $subject->id ?? null,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);
    }
}
