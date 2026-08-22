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
            return $this->redirectBasedOnRole($user->role);
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
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'piket' => redirect()->route('piket.dashboard'),
            'ortu', 'siswa' => redirect()->route('ortu.dashboard'),
            'wali_kelas' => redirect()->route('walikelas.dashboard'),
            'waka_kesiswaan', 'waka_sdm' => redirect()->route('waka.dashboard'),
            'kepala_sekolah' => redirect()->route('kepala.dashboard'),
            'satpam' => redirect()->route('satpam.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
