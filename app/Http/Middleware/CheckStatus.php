<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            $status = $request->user()->status ?? null;

            if ($status === 'active') {
                return $next($request);
            }

            // Jika status verify, arahkan ke login
            if ($status === 'verify') {
                return redirect('/login')->withErrors([
                    'email' => 'Akun Anda belum diverifikasi.'
                ]);
            }

            // Default: jika status tidak dikenal
            return redirect('/login')->withErrors([
                'email' => 'Status akun tidak valid.'
            ]);
    }
}
