<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class wawancaraquest extends Model
{
    protected $table = 'wwn_questions';

    protected $fillable = [
        'subject',
        'pertanyaan',
        'image_path',
    ];

    public function options()
    {
        return $this->hasMany(wawancaraoption::class, 'id_wwn');
    }
}
