<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($credentials['username']);
        $throttleKey = Str::transliterate(Str::lower($username) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('username'))->with('error', "Terlalu banyak percobaan login salah. Silakan coba lagi dalam {$seconds} detik.");
        }

        if (Auth::attempt(['username' => $username, 'password' => $credentials['password']])) {
            RateLimiter::clear($throttleKey);
            $user = Auth::user();

            if (!$user->aktif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
            }

            $request->session()->regenerate();
            $defaultRoute = $this->getDashboardRouteName($user->role);
            return redirect()->intended(route($defaultRoute));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withInput($request->only('username'))->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    protected function redirectBasedOnRole(string $role)
    {
        $routeName = $this->getDashboardRouteName($role);
        return redirect()->route($routeName);
    }

    protected function getDashboardRouteName(string $role): string
    {
        return match ($role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'piket' => 'piket.dashboard',
            'ortu', 'siswa' => 'ortu.dashboard',
            'wali_kelas' => 'walikelas.dashboard',
            'waka_sdm' => 'waka.dashboard',
            'waka_kesiswaan' => 'waka.monitoring-siswa',
            'waka_kurikulum' => 'waka-kurikulum.dashboard',
            'waka_sarpras' => 'waka.sarpras',
            'waka_humas' => 'waka.humas',
            'kepala_sekolah' => 'kepala.dashboard',
            'satpam' => 'satpam.dashboard',
            default => 'login',
        };
    }
}
