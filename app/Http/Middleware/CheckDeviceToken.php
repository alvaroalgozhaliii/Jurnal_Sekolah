<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\DeviceRequest;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya cek jika user terautentikasi
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin dibebaskan dari pembatasan 1 perangkat
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Jangan cegah request logout atau route pending
        if ($request->routeIs('logout') || $request->routeIs('device.pending') || $request->is('device-pending*')) {
            return $next($request);
        }

        $cookieToken = $request->cookie('jurnal_device_token');
        $dbToken = $user->device_token;

        // KASUS 1: User belum pernah mendaftarkan perangkat (Pertama kali login)
        if (empty($dbToken)) {
            $tokenToSet = $cookieToken ?: Str::uuid()->toString();
            $user->device_token = $tokenToSet;
            $user->save();

            $response = $next($request);
            if (!$cookieToken || $cookieToken !== $tokenToSet) {
                $response->withCookie(cookie()->forever('jurnal_device_token', $tokenToSet));
            }
            return $response;
        }

        // KASUS 2: Cookie perangkat cocok dengan yang terdaftar di database
        if ($cookieToken && $cookieToken === $dbToken) {
            return $next($request);
        }

        // KASUS 3: Cookie berbeda atau tidak ada di perangkat ini (Perangkat Baru / Ganti HP)
        // Cek apakah ada pengajuan pergantian perangkat yang SUDAH DISETUJUI oleh Admin
        $approvedRequest = DeviceRequest::where('id_user', $user->id_user)
            ->where('status', 'disetujui')
            ->orderBy('approved_at', 'desc')
            ->first();

        if ($approvedRequest) {
            // Jika request yang disetujui memiliki token yang sama dengan cookie saat ini
            // atau jika cookie belum ada, aktifkan token dari pengajuan yang disetujui
            $activeToken = $cookieToken ?: $approvedRequest->device_token;
            $user->device_token = $activeToken;
            $user->save();

            // Bersihkan request yang sudah dipakai
            $approvedRequest->delete();

            $response = $next($request);
            return $response->withCookie(cookie()->forever('jurnal_device_token', $activeToken));
        }

        // Cek apakah sudah ada pengajuan yang PENDING
        $pendingRequest = DeviceRequest::where('id_user', $user->id_user)
            ->where('status', 'pending')
            ->first();

        $tokenForRequest = $cookieToken ?: Str::uuid()->toString();

        if (!$pendingRequest) {
            // Buat request baru untuk persetujuan admin
            DeviceRequest::create([
                'id_user'      => $user->id_user,
                'device_token' => $tokenForRequest,
                'user_agent'   => $request->userAgent(),
                'ip_address'   => $request->ip(),
                'status'       => 'pending',
            ]);

            // Kirim notifikasi ke seluruh administrator
            $admins = User::where('role', 'admin')->where('aktif', true)->get();
            foreach ($admins as $admin) {
                Notifikasi::kirim(
                    $admin->id_user,
                    '🔔 Permintaan Login Perangkat Baru',
                    "Pengguna {$user->nama} ({$user->username}) terdeteksi login dari perangkat baru. Menunggu persetujuan Anda.",
                    route('admin.device-requests'),
                    'device_request'
                );
            }
        }

        $namaUser = $user->nama;
        $username = $user->username;

        // Logout user agar tidak bisa mengakses aplikasi sampai disetujui
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('device.pending')
            ->withCookie(cookie()->forever('jurnal_device_token', $tokenForRequest))
            ->with([
                'device_blocked_nama' => $namaUser,
                'device_blocked_username' => $username,
            ]);
    }
}
