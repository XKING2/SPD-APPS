<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrakResult extends Model
{
    protected $table = 'nilai_prak';
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
