<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if (!$user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif.');
        }

        // Always allow admin on general management routes unless specifically restricted
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Map 'siswa' requirement to 'ortu'
        $allowedRoles = [];
        foreach ($roles as $r) {
            $allowedRoles[] = $r;
            if ($r === 'siswa') {
                $allowedRoles[] = 'ortu';
            }
            if ($r === 'ortu') {
                $allowedRoles[] = 'siswa';
            }
        }

        if (!in_array($user->role, $allowedRoles)) {
            $targetRoleName = ucwords(str_replace('_', ' ', implode(' / ', $roles)));
            $currentRoleName = ucwords(str_replace('_', ' ', $user->role));
            
            // Simpan intended URL agar setelah login langsung ke halaman tujuan
            session()->put('url.intended', $request->fullUrl());
            Auth::logout();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('info', "Halaman ini memerlukan login sebagai {$targetRoleName}. Anda sebelumnya login sebagai {$currentRoleName}. Silakan login dengan akun {$targetRoleName}.");
        }

        return $next($request);
    }
}
