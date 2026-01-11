<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class exams extends Model
{
    protected $table = 'exams';
    protected $fillable = [
        'id_seleksi',
        'judul',
        'type',
        'duration',
        'status',
        'enrollment_key',
        'key_generated_at',
        'key_expired_at',
        'start_at',
        'end_at',
        'created_by',
        'id_desas'
    ];

    
    protected $casts = [
        'key_generated_at' => 'datetime',
        'key_expired_at'   => 'datetime',
        'start_at'         => 'datetime',
        'end_at'           => 'datetime',
    ];

    public function seleksi()
    {
        return $this->belongsTo(seleksi::class, 'id_seleksi');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function desa()
    {
        return $this->belongsTo(Desas::class, 'id_desas');
    }

    public const TYPES = [
        'tpu' => 'TPU',
        'wwn' => 'Wawancara',
        'orb' => 'Observasi',
    ];

    public function isKeyActive(): bool
    {
        return $this->key_expired_at?->isFuture() ?? false;
    }


}
