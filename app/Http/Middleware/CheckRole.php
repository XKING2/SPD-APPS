<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        $user = Auth::user();

        // Auth HARUS sudah ditangani oleh middleware auth
        if (!$user) {
            abort(401,'Anda Belum Login');
        }

        // 🔒 Status check (PENTING)
        if ($user->status !== 'actived') {
            Auth::logout();
            abort(403, 'Akun belum diverifikasi atau tidak aktif.');
        }

        // 🔐 Role check
        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }

}
