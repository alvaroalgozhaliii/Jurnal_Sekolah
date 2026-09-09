<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResetPasswordRequest;
use App\Models\Notifikasi;

class ResetPasswordAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = ResetPasswordRequest::with('user')->orderBy('created_at', 'desc');

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected', 'completed'])) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(15)->withQueryString();

        $counts = [
            'pending'   => ResetPasswordRequest::where('status', 'pending')->count(),
            'approved'  => ResetPasswordRequest::where('status', 'approved')->count(),
            'rejected'  => ResetPasswordRequest::where('status', 'rejected')->count(),
            'completed' => ResetPasswordRequest::where('status', 'completed')->count(),
            'all'       => ResetPasswordRequest::count(),
        ];

        return view('admin.reset-password-requests.index', compact('requests', 'counts', 'status'));
    }

    public function approve($id)
    {
        $req = ResetPasswordRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak pending.');
        }

        $req->status = 'approved';
        $req->approved_at = now();
        $req->save();

        if ($req->id_user) {
            Notifikasi::kirim(
                $req->id_user,
                'Pengajuan Reset Password Disetujui',
                'Pengajuan reset password Anda telah disetujui oleh Admin. Silakan lanjutkan pembuatan password baru.',
                null,
                'reset_approved'
            );
        }

        return back()->with('success', 'Pengajuan reset password berhasil disetujui! Pengaju dapat mereset sandinya sekarang.');
    }

    public function reject(Request $request, $id)
    {
        $req = ResetPasswordRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak pending.');
        }

        $catatan = $request->input('catatan', 'Pengajuan ditolak oleh Admin.');

        $req->status = 'rejected';
        $req->catatan = $catatan;
        $req->save();

        if ($req->id_user) {
            Notifikasi::kirim(
                $req->id_user,
                'Pengajuan Reset Password Ditolak',
                'Pengajuan reset password Anda ditolak oleh Admin. Catatan: ' . $catatan,
                null,
                'reset_rejected'
            );
        }

        return back()->with('success', 'Pengajuan reset password telah ditolak.');
    }
}
