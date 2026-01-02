<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrbResult extends Model
{
    protected $table = 'nilai_orb';
    protected $fillable = [
        'user_id',
        'kerapian',
        'kecepatan',
        'ketepatan',
        'efektifitas'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
