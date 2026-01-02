<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class wawancaraoption extends Model
{
    protected $table = 'wwn_options';

    protected $fillable = [
        'id_wwn',
        'label',
        'opsi_tulisan',
        'point',
    ];

    public function question()
    {
        return $this->belongsTo(
            wawancaraquest::class,
            'id_wwn',
            'id'
        );
    }
}
