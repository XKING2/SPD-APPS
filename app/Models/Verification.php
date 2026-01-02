<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unique_id',
        'otp',
        'type',
        'send_via',
        'resend',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
