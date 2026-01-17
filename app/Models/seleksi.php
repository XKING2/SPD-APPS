<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class seleksi extends Model
{
    protected $table = 'selections';
    protected $fillable = [
        'judul',
        'deskripsi',
        'tahun',
        'id_desas',
        'id_kecamatans'
    ];

    public function exams()
    {
        return $this->hasMany(
            exams::class,
            'id_seleksi',
            'id'
        );
    }

    public function desa()
    {
        return $this->belongsTo(
            Desas::class,
            'id_desas'
        );
    }


}
