<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            // Kalau role tidak cocok → redirect sesuai role-nya
            switch ($user->role) {
                case 'tu':
                    return redirect()->route('tu.dashboard');
                case 'dosen':
                    return redirect()->route('dosen.dashboard');
                case 'koordinator':
                    return redirect()->route('kaprodi.dashboard');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors('Akses ditolak!');
            }
        }

        return $next($request);
    }
}
