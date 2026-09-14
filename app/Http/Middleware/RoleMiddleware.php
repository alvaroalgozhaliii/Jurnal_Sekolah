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
            session()->put('url.intended', $request->fullUrl());
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
        }

        // Admin selalu diizinkan di seluruh route manajemen
        if ($user->role === 'admin') {
            return $this->addNoCacheHeaders($next($request));
        }

        $activeAccess = session('active_access', $user->role);

        // Periksa apakah pengguna memiliki salah satu dari role yang diizinkan
        $authorized = false;
        foreach ($roles as $role) {
            if ($this->checkRoleAccess($user, $activeAccess, $role)) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            $homeRoute = \App\Http\Controllers\AuthController::getDashboardRouteName($activeAccess);
            if (!\Illuminate\Support\Facades\Route::has($homeRoute)) {
                $homeRoute = \App\Http\Controllers\AuthController::getDashboardRouteName($user->role);
            }

            return redirect()->route($homeRoute)->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }

        return $this->addNoCacheHeaders($next($request));
    }

    protected function checkRoleAccess(\App\Models\User $user, string $activeAccess, string $requiredRole): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        // 1. Hak Akses Guru
        if ($requiredRole === 'guru') {
            return $user->isGuru();
        }

        // 2. Hak Akses Wali Kelas
        if ($requiredRole === 'wali_kelas' || $requiredRole === 'walikelas') {
            return $user->isWaliKelas();
        }

        // 3. Hak Akses Waka (General atau spesifik)
        if ($requiredRole === 'waka') {
            return $user->isWaka();
        }
        if (str_starts_with($requiredRole, 'waka_')) {
            return $user->role === $requiredRole || $activeAccess === $requiredRole || $user->isWaka();
        }

        // 4. Hak Akses Piket
        if ($requiredRole === 'piket') {
            return $user->isPiket();
        }

        // 5. Hak Akses Siswa / Ortu
        if ($requiredRole === 'siswa' || $requiredRole === 'ortu') {
            return $user->isSiswa() || $user->isOrtu();
        }

        // 6. Hak Akses Kepala Sekolah
        if ($requiredRole === 'kepala_sekolah') {
            return $user->isKepalaSekolah();
        }

        // 7. Hak Akses Satpam
        if ($requiredRole === 'satpam') {
            return $user->isSatpam();
        }

        return $user->role === $requiredRole || $activeAccess === $requiredRole;
    }

    protected function addNoCacheHeaders(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
