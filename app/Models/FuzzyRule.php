<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyRule extends Model
{
    protected $table = 'fuzzy_rules';
    protected $fillable = [
        'min_value',
        'max_value',
        'crisp_value',
    ];
}
