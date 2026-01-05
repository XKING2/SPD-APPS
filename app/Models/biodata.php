<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class biodata extends Model
{
    use HasFactory;
    protected $table = 'biodata';

    protected $fillable = [
        'id_user','kartu_keluarga','ktp','ijazah','cv','surat_pendaftaran','profile_img','validated_at','status'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_user');
    }
}
