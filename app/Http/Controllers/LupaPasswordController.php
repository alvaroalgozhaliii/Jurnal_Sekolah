<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\ResetPasswordRequest;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LupaPasswordController extends Controller
{
    public function showForm(Request $request)
    {
        $token = $request->cookie('reset_token') ?? $request->query('token');

        $activeRequest = null;
        if ($token) {
            $activeRequest = ResetPasswordRequest::where('reset_token', $token)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($activeRequest && $activeRequest->status === 'approved') {
                return redirect()->route('reset-password.form', ['token' => $activeRequest->reset_token]);
            }
        }

        return view('auth.lupa-password', compact('activeRequest', 'token'));
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'role_tipe' => 'required|in:ortu,guru_staf',
            'nisn_nik'  => 'required|string|max:50',
        ], [
            'role_tipe.required' => 'Pilih jenis akun terlebih dahulu.',
            'nisn_nik.required'  => 'NISN Anak atau NIK wajib diisi.',
        ]);

        $nisnNik = trim($validated['nisn_nik']);
        $user = null;
        $namaPengaju = null;

        if ($validated['role_tipe'] === 'ortu') {
            // Find student by NISN or NIS
            $siswa = Siswa::where('nisn', $nisnNik)->orWhere('nis', $nisnNik)->first();
            if (!$siswa) {
                return back()->withInput()->with('error', 'Data NISN Anak (' . $nisnNik . ') tidak ditemukan dalam sistem. Mohon periksa kembali.');
            }

            // Find parent user linked to student
            $parentUser = $siswa->ortu()->first();
            if ($parentUser) {
                $user = $parentUser;
            } elseif ($siswa->id_user) {
                $user = User::find($siswa->id_user);
            }

            if (!$user) {
                return back()->withInput()->with('error', 'Akun Orang Tua untuk NISN (' . $nisnNik . ') belum terdaftar. Silakan hubungi Admin.');
            }

            $namaPengaju = 'Ortu dari ' . $siswa->nama . ' (NISN: ' . $siswa->nisn . ')';
        } else {
            // Guru / Staff / Other roles: Find by NIK or NIP
            $guru = Guru::where('nik', $nisnNik)->orWhere('nip', $nisnNik)->first();
            if ($guru && $guru->user) {
                $user = $guru->user;
                $namaPengaju = $guru->nama . ' (Guru/Staf)';
            } else {
                $user = User::where('nik', $nisnNik)
                    ->orWhere('nip', $nisnNik)
                    ->whereNotIn('role', ['ortu', 'siswa'])
                    ->first();
                if ($user) {
                    $namaPengaju = $user->nama . ' (' . ucfirst($user->role) . ')';
                }
            }

            if (!$user) {
                return back()->withInput()->with('error', 'Data NIK/NIP (' . $nisnNik . ') tidak ditemukan untuk kategori Guru/Staf.');
            }
        }

        // Check if there is an existing pending request for this user or token
        $existing = ResetPasswordRequest::where('id_user', $user->id_user)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            $token = $existing->reset_token;
            // Refresh cookie
            Cookie::queue(cookie('reset_token', $token, 43200, '/', null, false, false));
            return redirect()->route('lupa-password', ['token' => $token])
                ->with('info', 'Anda sudah memiliki pengajuan reset password yang sedang menunggu persetujuan Admin.');
        }

        $token = Str::random(64);

        $resetReq = ResetPasswordRequest::create([
            'id_user'      => $user->id_user,
            'role_tipe'    => $validated['role_tipe'],
            'nisn_nik'     => $nisnNik,
            'nama_pengaju' => $namaPengaju,
            'reset_token'  => $token,
            'status'       => 'pending',
        ]);

        // Send notification to all admin users
        Notifikasi::kirimKeRole(
            'admin',
            'Pengajuan Reset Password & Username',
            'Pengajuan reset password dari ' . $namaPengaju . ' menunggu persetujuan.',
            route('admin.reset-password.index'),
            'reset_password'
        );

        // Save persistent cookie (30 days)
        Cookie::queue(cookie('reset_token', $token, 43200, '/', null, false, false));

        return redirect()->route('lupa-password', ['token' => $token])
            ->with('success', 'Pengajuan reset password & username berhasil dikirim ke Admin. Silakan tunggu persetujuan.');
    }

    public function checkStatusApi(Request $request)
    {
        $token = $request->query('token') ?? $request->cookie('reset_token');

        if (!$token) {
            return response()->json(['status' => 'not_found']);
        }

        $resetReq = ResetPasswordRequest::where('reset_token', $token)->first();

        if (!$resetReq) {
            return response()->json(['status' => 'not_found']);
        }

        $redirectUrl = null;
        if ($resetReq->status === 'approved') {
            $redirectUrl = route('reset-password.form', ['token' => $token]);
        }

        return response()->json([
            'status'        => $resetReq->status,
            'redirect_url'  => $redirectUrl,
            'nama_pengaju'  => $resetReq->nama_pengaju,
            'updated_at'    => $resetReq->updated_at ? $resetReq->updated_at->format('H:i, d M Y') : '',
        ]);
    }

    public function showResetForm($token)
    {
        $resetReq = ResetPasswordRequest::where('reset_token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$resetReq) {
            return redirect()->route('lupa-password')
                ->with('error', 'Pengajuan reset password tidak ditemukan, sudah kedaluwarsa, atau belum disetujui oleh Admin.');
        }

        $user = $resetReq->user;
        if (!$user) {
            return redirect()->route('lupa-password')->with('error', 'Data pengguna tidak ditemukan.');
        }

        return view('auth.reset-password', compact('resetReq', 'user', 'token'));
    }

    public function processReset(Request $request, $token)
    {
        $resetReq = ResetPasswordRequest::where('reset_token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$resetReq) {
            return redirect()->route('lupa-password')
                ->with('error', 'Permintaan reset password tidak sah atau sudah kedaluwarsa.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = $resetReq->user;
        $user->password = Hash::make($request->password);
        $user->save();

        $resetReq->status = 'completed';
        $resetReq->completed_at = now();
        $resetReq->save();

        // Expire the token cookie
        Cookie::queue(Cookie::forget('reset_token'));

        return redirect()->route('login')
            ->with('success', 'Password berhasil diperbarui! Silakan login dengan username ' . $user->username . ' dan password baru Anda.');
    }
}
