<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamOption extends Model
{
    protected $table = 'tpu_options';

    protected $fillable = [
        'id_Pertanyaan',
        'label',
        'opsi_tulisan',
    ];

    public function question()
    {
        return $this->belongsTo(
            ExamQuestion::class,
            'id_Pertanyaan'
        );
    }
}
