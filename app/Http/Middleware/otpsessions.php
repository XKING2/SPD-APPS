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
        // Kalau user sedang OTP process
        if (session()->has('otp_user_id')) {

            $routeName = optional($request->route())->getName();

            $allowedRoutes = [
                'otp.form',
                'otp.verify',
                'otp.resend',
                'otp.cancel',
            ];

            if (!in_array($routeName, $allowedRoutes)) {
                return redirect()->route('otp.form');
            }
        }

        return $next($request);
    }

}
