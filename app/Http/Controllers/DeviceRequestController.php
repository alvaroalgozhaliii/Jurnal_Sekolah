<?php

namespace App\Http\Controllers;

use App\Models\DeviceRequest;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeviceRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statusFilter = $request->get('status');

        $query = DeviceRequest::with(['user', 'approvedBy']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            })->orWhere('ip_address', 'like', "%{$search}%");
        }

        $requests = $query->orderByRaw("FIELD(status, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $pendingCount = DeviceRequest::where('status', 'pending')->count();
        $disetujuiCount = DeviceRequest::where('status', 'disetujui')->count();
        $ditolakCount = DeviceRequest::where('status', 'ditolak')->count();

        // Data pengguna untuk daftar reset perangkat
        $usersWithDevice = User::whereNotNull('device_token')
            ->where('role', '!=', 'admin')
            ->orderBy('nama')
            ->paginate(10, ['*'], 'users_page');

        return view('admin.device-requests.index', compact(
            'requests',
            'pendingCount',
            'disetujuiCount',
            'ditolakCount',
            'usersWithDevice',
            'search',
            'statusFilter'
        ));
    }

    public function approve($id)
    {
        $deviceReq = DeviceRequest::findOrFail($id);
        $admin = Auth::user();

        $deviceReq->update([
            'status'      => 'disetujui',
            'approved_by' => $admin->id_user,
            'approved_at' => Carbon::now(),
        ]);

        $user = $deviceReq->user;
        if ($user) {
            $user->device_token = $deviceReq->device_token;
            $user->save();

            Notifikasi::kirim(
                $user->id_user,
                '✅ Perangkat Baru Disetujui',
                'Permintaan pergantian perangkat Anda telah disetujui oleh Administrator. Silakan login kembali.',
                route('login'),
                'device_approved'
            );
        }

        return back()->with('success', "Permintaan perangkat untuk {$user?->nama} ({$user?->username}) telah disetujui.");
    }

    public function reject(Request $request, $id)
    {
        $deviceReq = DeviceRequest::findOrFail($id);
        $admin = Auth::user();

        $deviceReq->update([
            'status'      => 'ditolak',
            'approved_by' => $admin->id_user,
            'approved_at' => Carbon::now(),
        ]);

        $user = $deviceReq->user;
        if ($user) {
            Notifikasi::kirim(
                $user->id_user,
                '❌ Permintaan Perangkat Ditolak',
                'Permintaan pergantian perangkat Anda ditolak oleh Administrator. Hubungi pihak sekolah untuk informasi lebih lanjut.',
                null,
                'device_rejected'
            );
        }

        return back()->with('success', "Permintaan perangkat untuk {$user?->nama} telah ditolak.");
    }

    public function reset($id)
    {
        $user = User::findOrFail($id);
        $user->device_token = null;
        $user->save();

        DeviceRequest::where('id_user', $id)->delete();

        Notifikasi::kirim(
            $user->id_user,
            '🔄 Perangkat Akun Direset',
            'Kaitan perangkat akun Anda telah direset oleh Administrator. Anda sekarang dapat login kembali dari perangkat baru.',
            route('login'),
            'device_reset'
        );

        return back()->with('success', "Kaitan perangkat untuk pengguna {$user->nama} ({$user->username}) berhasil direset.");
    }

    public function simpanKeterangan(Request $request)
    {
        $request->validate([
            'username'   => 'required|string',
            'keterangan' => 'required|string|max:500',
        ]);

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan.');
        }

        $pendingReq = DeviceRequest::where('id_user', $user->id_user)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($pendingReq) {
            $pendingReq->update([
                'keterangan' => $request->keterangan,
            ]);
            return back()->with('success', 'Alasan pergantian perangkat berhasil dikirim ke Administrator.');
        }

        return back()->with('info', 'Tidak ada pengajuan perangkat yang sedang menunggu.');
    }
}
