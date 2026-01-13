<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class biodata extends Model
{
    use HasFactory;
    protected $table = 'biodata';

    protected $fillable = [
        'id_user',
        'id_formasi',
        'id_kebutuhan',
        'id_desas',
        'kartu_keluarga',
        'ktp',
        'ijazah',
        'cv',
        'surat_pendaftaran',
        'profile_img',
        'validated_at',
        'status',
        'notified',
        'notified_admin'
    ];

    public function formasi() {
        return $this->belongsTo(Formasi::class, 'id_formasi');
    }

    public function kebutuhan() {
        return $this->belongsTo(FormasiKebutuhan::class, 'id_kebutuhan');
    }

    public function user() {
        return $this->belongsTo(User::class, 'id_user');
    }
}
