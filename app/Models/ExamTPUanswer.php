<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamTPUanswer extends Model
{
    protected $table = 'tpu_answer';

    protected $fillable = [
        'user_id',
        'exams_question',
        'exams_option',
    ];

    public function options()
    {
        return $this->belongsTo(ExamOption::class, 'exams_option');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exams_question');
    }
}
