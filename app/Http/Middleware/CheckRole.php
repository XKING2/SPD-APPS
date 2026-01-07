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
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Silakan login terlebih dahulu.']);
        }

        $user = Auth::user();

        // Jika tidak ada role yang diberikan, izinkan akses
        if (empty($roles)) {
            return $next($request);
        }

        // Parsing roles (support format: 'admin' atau 'admin,user')
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (strpos($role, ',') !== false) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $role));
            } else {
                $allowedRoles[] = $role;
            }
        }

        // Trim whitespace dari setiap role
        $allowedRoles = array_map('trim', $allowedRoles);

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
    
}
