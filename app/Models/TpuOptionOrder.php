<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpuOptionOrder extends Model
{
    protected $table = 'tpu_option_orders';

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

