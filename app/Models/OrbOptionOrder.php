<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrbOptionOrder extends Model
{
    protected $table = 'orb_option_order';

    protected $fillable = [
        'exam_id',
        'user_id',
        'question_id',
        'option_order',
    ];

    protected $casts = [
        'option_order' => 'array',
    ];
}
