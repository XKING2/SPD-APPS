<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyScore extends Model
{
    protected $table = 'fuzzy_scores';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'id_seleksi',
        'type',
        'score_raw',
        'score_crisp',
        'fuzzy_rule_id',
        'created_at'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cripsScore() {
        return $this->belongsTo(FuzzyRule::class, 'fuzzy_rule_id');
    }

    public function seleksi()
    {
        return $this->belongsTo(seleksi::class,'seleksi_id');
    }
}
