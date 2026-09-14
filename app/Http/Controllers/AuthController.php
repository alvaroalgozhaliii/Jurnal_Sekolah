<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $input = trim($credentials['username']);
        $throttleKey = Str::transliterate(Str::lower($input) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('username'))->with('error', "Terlalu banyak percobaan login salah. Silakan coba lagi dalam {$seconds} detik.");
        }

        // Resolusi Akun: Username / NIP / NISN
        $user = $this->resolveUser($input);

        if ($user && Hash::check($credentials['password'], $user->password)) {
            RateLimiter::clear($throttleKey);

            if (!$user->aktif) {
                return back()->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            // 1. Alur Guru (Cek tugas tambahan)
            if ($user->isGuru()) {
                $availableAccesses = $user->getAvailableAccesses();

                // Jika memiliki lebih dari 1 akses (misal Guru + Wali Kelas, atau Guru + Waka)
                if (count($availableAccesses) > 1) {
                    session(['available_accesses' => array_keys($availableAccesses)]);
                    return redirect()->route('pilih-akses');
                }

                // Guru biasa (tanpa tugas tambahan) langsung ke Dashboard Guru
                session(['active_access' => 'guru']);
                return redirect()->intended(route('guru.dashboard'));
            }

            // 2. Alur Siswa / Ortu
            if ($user->isSiswa() || $user->isOrtu()) {
                session(['active_access' => 'ortu']);
                return redirect()->intended(route('ortu.dashboard'));
            }

            // 3. Alur Piket
            if ($user->isPiket()) {
                session(['active_access' => 'piket']);
                return redirect()->intended(route('piket.dashboard'));
            }

            // 4. Alur Role Lain (Admin, Kepala Sekolah, Satpam, Waka standalone)
            $defaultRoute = $this->getDashboardRouteName($user->role);
            session(['active_access' => $user->role]);
            return redirect()->intended(route($defaultRoute));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withInput($request->only('username'))->with('error', 'Username, NIP, NISN, atau password salah.');
    }

    public function logout(Request $request)
    {
        session()->forget(['active_access', 'available_accesses']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    protected function resolveUser(string $input): ?User
    {
        // 1. Cek langsung via Username di users
        $user = User::where('username', $input)->first();
        if ($user) {
            return $user;
        }

        $cleanNip = str_replace([' ', '-', '.'], '', $input);

        // 2. Cek via NIP di tabel users
        $userByNip = User::where('nip', $input)
            ->orWhereRaw("REPLACE(REPLACE(REPLACE(nip, ' ', ''), '-', ''), '.', '') = ?", [$cleanNip])
            ->first();
        if ($userByNip) {
            return $userByNip;
        }

        // 3. Cek via NIP di tabel guru (relasi user)
        $guru = Guru::where('nip', $input)
            ->orWhereRaw("REPLACE(REPLACE(REPLACE(nip, ' ', ''), '-', ''), '.', '') = ?", [$cleanNip])
            ->first();
        if ($guru && $guru->user) {
            return $guru->user;
        }

        // 4. Cek via NISN di tabel siswa (relasi user)
        $siswa = Siswa::where('nisn', $input)->first();
        if ($siswa && $siswa->user) {
            return $siswa->user;
        }

        // 5. Cek via format username siswa: siswa.<nisn>
        $userSiswa = User::where('username', 'siswa.' . $input)->first();
        if ($userSiswa) {
            return $userSiswa;
        }

        return null;
    }

    protected function redirectBasedOnRole(User $user)
    {
        $activeAccess = session('active_access');
        if ($activeAccess) {
            $routeName = $this->getDashboardRouteName($activeAccess);
            return redirect()->route($routeName);
        }

        $routeName = $this->getDashboardRouteName($user->role);
        return redirect()->route($routeName);
    }

    public static function getDashboardRouteName(string $role): string
    {
        return match ($role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'piket' => 'piket.dashboard',
            'ortu', 'siswa' => 'ortu.dashboard',
            'wali_kelas', 'walikelas' => 'walikelas.dashboard',
            'waka' => 'waka.dashboard',
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
