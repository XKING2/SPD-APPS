<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormasiKebutuhan extends Model
{
    protected $table = 'kebutuhan_formasi';
    protected $fillable = [
        'id_formasi',
        'nama_kebutuhan',
        'jumlah',

    ];

    public function formasi()
    {
        return $this->belongsTo(
            Formasi::class,
            'id_formasi'
        );
    }

}
