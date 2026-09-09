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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LupaPasswordController extends Controller
{
    public function showForm(Request $request)
    {
        return view('auth.lupa-password');
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'role_tipe' => 'required|in:ortu,guru_staf',
            'nisn_nik'  => 'required|string|max:50',
        ], [
            'role_tipe.required' => 'Pilih kategori pengajuan (NISN Anak atau NIP / NIK Guru/Staf) terlebih dahulu.',
            'nisn_nik.required'  => 'Masukkan nomor NISN Anak atau NIP / NIK.',
        ]);

        $nisnNik = trim($validated['nisn_nik']);
        $roleTipe = $validated['role_tipe'];
        $user = null;
        $namaPengaju = null;

        if ($roleTipe === 'ortu') {
            // Find student by NISN or NIS
            $siswaQuery = Siswa::query();
            $hasNisn = Schema::hasColumn('siswa', 'nisn');
            $hasNis  = Schema::hasColumn('siswa', 'nis');

            if ($hasNisn && $hasNis) {
                $siswaQuery->where(function($q) use ($nisnNik) {
                    $q->where('nisn', $nisnNik)->orWhere('nis', $nisnNik);
                });
            } elseif ($hasNisn) {
                $siswaQuery->where('nisn', $nisnNik);
            } elseif ($hasNis) {
                $siswaQuery->where('nis', $nisnNik);
            }

            $siswa = $siswaQuery->first();

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
                return back()->withInput()->with('error', 'Akun Orang Tua untuk NISN (' . $nisnNik . ') belum terdaftar di sistem.');
            }

            $identifier = $siswa->nisn ?? ($siswa->nis ?? $nisnNik);
            $namaPengaju = 'Ortu dari ' . $siswa->nama . ' (NISN: ' . $identifier . ')';
        } else {
            // Find Guru or Staff by NIP or NIK
            $guruQuery = Guru::query();
            $hasGuruNik = Schema::hasColumn('guru', 'nik');
            $hasGuruNip = Schema::hasColumn('guru', 'nip');

            if ($hasGuruNik && $hasGuruNip) {
                $guruQuery->where(function($q) use ($nisnNik) {
                    $q->where('nip', $nisnNik)->orWhere('nik', $nisnNik);
                });
            } elseif ($hasGuruNip) {
                $guruQuery->where('nip', $nisnNik);
            } elseif ($hasGuruNik) {
                $guruQuery->where('nik', $nisnNik);
            }

            $guru = $guruQuery->first();

            if ($guru && $guru->user) {
                $user = $guru->user;
                $namaPengaju = $guru->nama . ' (Guru/Staf)';
            } else {
                // Check User table directly by NIP, NIK, or Username
                $userQuery = User::query();
                $hasUserNik = Schema::hasColumn('users', 'nik');
                $hasUserNip = Schema::hasColumn('users', 'nip');

                if ($hasUserNik && $hasUserNip) {
                    $userQuery->where(function($q) use ($nisnNik) {
                        $q->where('nip', $nisnNik)->orWhere('nik', $nisnNik)->orWhere('username', $nisnNik);
                    });
                } elseif ($hasUserNip) {
                    $userQuery->where(function($q) use ($nisnNik) {
                        $q->where('nip', $nisnNik)->orWhere('username', $nisnNik);
                    });
                } elseif ($hasUserNik) {
                    $userQuery->where(function($q) use ($nisnNik) {
                        $q->where('nik', $nisnNik)->orWhere('username', $nisnNik);
                    });
                } else {
                    $userQuery->where('username', $nisnNik);
                }

                $user = $userQuery->whereNotIn('role', ['ortu', 'siswa'])->first();

                if ($user) {
                    $namaPengaju = $user->nama . ' (' . ucfirst($user->role) . ')';
                }
            }

            if (!$user) {
                return back()->withInput()->with('error', 'Data NIP / NIK (' . $nisnNik . ') tidak ditemukan untuk kategori Guru/Staf.');
            }
        }

        // GATE CHECK: If user enters NISN / NIP and Admin HAS APPROVED their request -> Immediately open Reset Password Page!
        $approvedReq = ResetPasswordRequest::where('id_user', $user->id_user)
            ->where('status', 'approved')
            ->first();

        if ($approvedReq) {
            Cookie::queue(cookie('reset_token', $approvedReq->reset_token, 43200, '/', null, false, false));
            return redirect()->route('reset-password.form', ['token' => $approvedReq->reset_token]);
        }

        // Check if there is an existing PENDING request for this user
        $existing = ResetPasswordRequest::where('id_user', $user->id_user)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            $token = $existing->reset_token;
            Cookie::queue(cookie('reset_token', $token, 43200, '/', null, false, false));
            return back()->withInput()->with('info', 'Pengajuan reset password untuk ' . $namaPengaju . ' sedang dalam antrean verifikasi Admin. Silakan tunggu persetujuan Admin.');
        }

        // Create new PENDING request
        $token = Str::random(64);

        $resetReq = ResetPasswordRequest::create([
            'id_user'      => $user->id_user,
            'role_tipe'    => $roleTipe,
            'nisn_nik'     => $nisnNik,
            'nama_pengaju' => $namaPengaju,
            'reset_token'  => $token,
            'status'       => 'pending',
        ]);

        // Send notification to Admin users
        Notifikasi::kirimKeRole(
            'admin',
            'Pengajuan Reset Password & Username',
            'Pengajuan reset password dari ' . $namaPengaju . ' (' . ($roleTipe === 'ortu' ? 'NISN: ' : 'NIP/NIK: ') . $nisnNik . ') menunggu persetujuan.',
            route('admin.reset-password.index'),
            'reset_password'
        );

        // Save persistent cookie (30 days)
        Cookie::queue(cookie('reset_token', $token, 43200, '/', null, false, false));

        return back()->withInput()->with('success', 'Pengajuan reset password untuk ' . $namaPengaju . ' berhasil dikirim ke Admin. Silakan tunggu persetujuan Admin.');
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

        // Expire cookie
        Cookie::queue(Cookie::forget('reset_token'));

        return redirect()->route('login')
            ->with('success', 'Password berhasil diperbarui! Silakan login dengan username ' . $user->username . ' dan password baru Anda.');
    }
}
