<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrakResult extends Model
{
    protected $table = 'nilai_prak';
    protected $fillable = [
        'user_id',
        'kop_surat',
        'format_dokumen',
        'layout_ttd',
        'manajemen_file_waktu',
        'format_visualisasi_tabel',
        'fungsi_logika',
        'fungsi_lanjutan',
        'format_data',
        'output_ttd',
        'manajemen_file_excel',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
