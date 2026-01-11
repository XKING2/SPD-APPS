<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrbQuest extends Model
{
    protected $table = 'orb_questions';

    protected $fillable = [
        'subject',
        'pertanyaan',
        'image_path',
    ];

    public function options()
    {
        return $this->hasMany(OrbOption::class, 'id_orb');
    }
}
