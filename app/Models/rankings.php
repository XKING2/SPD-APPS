<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rankings extends Model
{
    protected $table = 'rankings';
    protected $fillable = [
        'id_seleksi',
        'user_id',
        'nilai_saw',
        'peringkat',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seleksi()
    {
        return $this->belongsTo(seleksi::class,'id_seleksi');
    }
}
