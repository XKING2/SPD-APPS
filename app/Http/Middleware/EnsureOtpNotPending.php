<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpNotPending
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('otp_user_id')) {
            return redirect()->route('otp.form')
                ->withErrors([
                    'otp' => 'Selesaikan verifikasi OTP terlebih dahulu.'
                ]);
        }

        return $next($request);
    }
}
