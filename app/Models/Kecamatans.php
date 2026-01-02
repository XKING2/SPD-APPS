<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatans extends Model
{
     protected $table = 'kecamatans';

    protected $fillable = [
        'nama_kecamatan'
    ];

    public function desas()
    {
        return $this->hasMany(Desas::class, 'id_kecamatans');
    }
}
