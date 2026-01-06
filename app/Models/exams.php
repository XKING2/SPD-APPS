<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class exams extends Model
{
    protected $table = 'exams';
    protected $fillable = [
        'id_seleksi',
        'judul',
        'type',
        'duration',
        'status',
        'enrollment_key',
        'key_generated_at',
        'key_expired_at',
        'start_at',
        'end_at',
        'created_by',
        'id_desas'
    ];

    public function seleksi()
    {
        return $this->belongsTo(
            seleksi::class,
            'id_seleksi'
        );
    }


    public function questions()
    {
        return $this->hasMany(
            ExamQuestion::class,
            'id_exam', 
            'id'       
        );
    }

    

    public function wawancara()
    {
        return $this->hasMany(
            wawancaraquest::class,
            'id_exams', // foreign key di wwn_questions
            'id'       // primary key di exams
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function desa()
    {
        return $this->belongsTo(Desas::class, 'id_desas');
    }

    public const TYPES = [
        'tpu' => 'TPU',
        'wwn' => 'Wawancara',
    ];



}
