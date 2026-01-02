<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkbiodata
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

            $user = Auth::user();

            // belum login
            if (!$user) {
                abort(403);
            }

            // user belum punya biodata
            if (!$user->biodata) {
                return redirect()->route('userdashboard')
                    ->with('error', 'Lengkapi biodata terlebih dahulu.');
            }

            // biodata belum divalidasi admin
            if ($user->biodata->status !== 'valid') {
                return redirect()->route('userdashboard')
                    ->with('error', 'Biodata Anda belum divalidasi admin.');
            }

            return $next($request);
    }
}
