<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class otpsessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('otp_user_id')) {

            // whitelist route yang BOLEH diakses
            $allowedRoutes = [
                'otp.form',
                'otp.verify',
                'otp.cancel',
            ];

            $routeName = optional($request->route())->getName();

            if (!in_array($routeName, $allowedRoutes)) {
                return redirect()->route('otp.form')
                 ->withErrors([
                    'otp' => 'Selesaikan verifikasi OTP terlebih dahulu.'
                ]);
            }
        }

        return $next($request);
    }

}
