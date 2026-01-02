<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desas extends Model
{
    protected $table = 'desas';

    protected $fillable = [
        'id_kecamatans',
        'nama_desa'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatans::class, 'id_kecamatans');
    }

}
