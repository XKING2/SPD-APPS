<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResultExam extends Model
{
    use HasFactory;

    protected $table = 'exam_user_results';

    protected $fillable = [
        'user_id',
        'exam_id',
        'type',
        'score',
        'is_submitted',
        'submitted_at',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // ================= RELATION =================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(exams::class, 'exam_id');
    }
}
