<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if (!$user->aktif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda tidak aktif.');
            }

            $request->session()->regenerate();
            $defaultRoute = $this->getDashboardRouteName($user->role);
            return redirect()->intended(route($defaultRoute));
        }

        return back()->with('error', 'Username atau password salah.');
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
            'waka_kesiswaan', 'waka_sdm' => 'waka.dashboard',
            'waka_kurikulum' => 'waka-kurikulum.dashboard',
            'kepala_sekolah' => 'kepala.dashboard',
            'satpam' => 'satpam.dashboard',
            default => 'login',
        };
    }
}
