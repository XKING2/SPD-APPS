<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{

    protected $table = 'tpu_questions';
    protected $fillable = [
        'id_exam',
        'subject',
        'code_pertanyaan',
        'pertanyaan',
        'image_name',
        'jawaban_benar',
    ];

    public function options()
    {
        return $this->hasMany(
            ExamOption::class,
            'id_Pertanyaan'
        );
    }
}
