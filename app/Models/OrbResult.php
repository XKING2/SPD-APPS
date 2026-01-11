<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrbResult extends Model
{
    protected $table = 'orb_result';

    protected $fillable = [
        'user_id',
        'orb_question',
        'orb_option',
    ];

    public function options()
    {
        return $this->belongsTo(OrbOption::class, 'orb_option');
    }

    public function question()
    {
        return $this->belongsTo(OrbQuest::class, 'orb_question');
    }
}
