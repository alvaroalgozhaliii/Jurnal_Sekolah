<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanIzin;
use App\Models\DispenLog;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\WhatsAppService;

class KepalaSekolahController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index()
    {
        $pendingStatuses = ['pending_kepala', 'disetujui_waka'];

        $pengajuanPending = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju', 'wakaApprover'])
            ->where('kategori', 'izin_guru')
            ->whereIn('status', $pendingStatuses)
            ->orderBy('created_at', 'desc')
            ->get();

        $pengajuanRiwayat = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju', 'wakaApprover', 'kepalaApprover'])
            ->where('kategori', 'izin_guru')
            ->where(function ($query) {
                $query->whereNotIn('status', ['pending_kepala', 'disetujui_waka'])
                    ->orWhereNotNull('id_kepala_approver');
            })
            ->where(function ($query) {
                $query->whereIn('status', ['disetujui_kepala', 'ditolak_kepala', 'completed', 'verified'])
                    ->orWhereNotNull('id_kepala_approver');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $totalPending = $pengajuanPending->count();
        $totalDisetujui = PengajuanIzin::where('kategori', 'izin_guru')
            ->whereIn('status', ['disetujui_kepala', 'completed', 'verified'])
            ->count();
        $totalDitolak = PengajuanIzin::where('kategori', 'izin_guru')
            ->where('status', 'ditolak_kepala')
            ->count();

        return view('kepala.dashboard', compact(
            'pengajuanPending',
            'pengajuanRiwayat',
            'totalPending',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    public function daftarPersetujuan()
    {
        $pendingStatuses = ['pending_kepala', 'disetujui_waka'];

        $pendingList = PengajuanIzin::with(['guru', 'pengaju', 'wakaApprover'])
            ->where('kategori', 'izin_guru')
            ->whereIn('status', $pendingStatuses)
            ->orderBy('created_at', 'desc')
            ->get();

        $disetujuiList = PengajuanIzin::with(['guru', 'pengaju', 'wakaApprover', 'kepalaApprover'])
            ->where('kategori', 'izin_guru')
            ->whereIn('status', ['disetujui_kepala', 'completed', 'verified'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ditolakList = PengajuanIzin::with(['guru', 'pengaju', 'wakaApprover', 'kepalaApprover'])
            ->where('kategori', 'izin_guru')
            ->where('status', 'ditolak_kepala')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kepala.index', compact('pendingList', 'disetujuiList', 'ditolakList'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanIzin::with([
            'guru',
            'pengaju',
            'piketApprover',
            'wakaApprover',
            'kepalaApprover',
            'logs.user'
        ])->findOrFail($id);

        return view('kepala.show', compact('pengajuan'));
    }

    public function prosesKeputusan(Request $request, $id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanIzin::findOrFail($id);

        $request->validate([
            'catatan' => 'nullable|string',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $namaGuru = $pengajuan->guru?->nama ?? $pengajuan->pengaju?->nama ?? 'Guru';

        if ($request->keputusan === 'setujui') {
            $statusSesudah = 'disetujui_kepala';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_kepala_approver' => $user->id_user,
                'catatan_kepala' => $request->catatan,
                'tgl_kepala' => now(),
            ]);

            // Catat log audit
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Disetujui penuh oleh Kepala Sekolah'
            );

            // Notifikasi In-App ke Guru / Pemohon
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Dispen Guru Disetujui Kepala Sekolah',
                "Pengajuan dispen Anda telah disetujui resmi oleh Kepala Sekolah. Catatan: " . ($request->catatan ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Notifikasi In-App ke Waka SDM
            Notifikasi::kirimKeRole(
                'waka_sdm',
                'Dispen Guru Selesai Disetujui Kepsek',
                "Pengajuan dispen guru {$namaGuru} telah disetujui resmi oleh Kepala Sekolah.",
                route('waka.persetujuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            // Kirim WhatsApp Selesai ke Guru
            $waResult = $this->waService->kirimNotifSelesaiKeGuru($pengajuan);

            $pesan = "Pengajuan dispensasi guru {$namaGuru} TELAH RESMI DISETUJUI oleh Kepala Sekolah.";
            if (isset($waResult) && !$waResult['success']) {
                $pesan .= ' (' . $waResult['message'] . ')';
            }

            return redirect()->route('kepala.persetujuan.show', $pengajuan->id_pengajuan)->with('success', $pesan);
        } else {
            $statusSesudah = 'ditolak_kepala';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_kepala_approver' => $user->id_user,
                'catatan_kepala' => $request->catatan,
                'tgl_kepala' => now(),
            ]);

            // Catat log audit
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Ditolak oleh Kepala Sekolah'
            );

            // Notifikasi In-App ke Guru / Pemohon
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Dispen Guru Ditolak Kepala Sekolah',
                "Pengajuan dispen Anda ditolak oleh Kepala Sekolah. Catatan: " . ($request->catatan ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            return redirect()->route('kepala.persetujuan.show', $pengajuan->id_pengajuan)->with('success', "Pengajuan dispensasi guru {$namaGuru} telah DITOLAK oleh Kepala Sekolah.");
        }
    }
}
