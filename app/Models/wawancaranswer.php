<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class wawancaranswer extends Model
{
    protected $table = 'wwn_answer';

    protected $fillable = [
        'user_id',
        'wawancara_question',
        'wawancara_option',
    ];

    public function options()
    {
        return $this->belongsTo(wawancaraoption::class, 'wawancara_option');
    }

    public function question()
    {
        return $this->belongsTo(wawancaraquest::class, 'wawancara_question');
    }
}
