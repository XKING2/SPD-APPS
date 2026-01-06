<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formasi extends Model
{
    protected $table = 'formasis';
    protected $fillable = [
        'id_seleksi',
        'id_desas',
        'tahun',

    ];

    public function seleksi()
    {
        return $this->belongsTo(
            seleksi::class,
            'id_seleksi'
        );
    }

    public function kebutuhan()
    {
        return $this->hasMany(
            FormasiKebutuhan::class,
            'id_formasi'
        );
    }

    public function desa()
    {
        return $this->belongsTo(Desas::class, 'id_desas');
    }

}
