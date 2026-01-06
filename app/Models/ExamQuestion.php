<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $table = 'tpu_questions';

    protected $fillable = [
        'subject',
        'pertanyaan',
        'image_name',
        'correct_option_id',
    ];

    public function options()
    {
        return $this->hasMany(
            ExamOption::class,
            'id_Pertanyaan'
        );
    }

    public function correctOption()
    {
        return $this->belongsTo(
            ExamOption::class,
            'correct_option_id'
        );
    }
}
