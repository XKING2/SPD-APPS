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
        if (!session()->has('otp_user_id')) {
            return $next($request);
        }

        $route = $request->route();

        if (!$route) {
            return $next($request); // penting: cegah loop head / ajax
        }

        $routeName = $route->getName();

        $allowedRoutes = [
            'otp.form',
            'otp.verify',
            'otp.resend',
            'otp.cancel',
        ];

        // kalau bukan halaman OTP → redirect SATU KALI
        if (!in_array($routeName, $allowedRoutes)) {
            return redirect()->route('otp.form');
        }

        return $next($request);
    }


}
