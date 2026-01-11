<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrbOption extends Model
{
    protected $table = 'orb_options';

    protected $fillable = [
        'id_orb',
        'label',
        'opsi_tulisan',
        'point',
    ];

    public function question()
    {
        return $this->belongsTo(
            OrbQuest::class,
            'id_orb',
            'id'
        );
    }
}
