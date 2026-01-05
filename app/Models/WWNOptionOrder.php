<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WWNOptionOrder extends Model
{
    protected $table = 'wawancara_option_orders';

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
