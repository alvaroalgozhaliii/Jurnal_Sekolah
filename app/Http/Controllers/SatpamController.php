<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanIzin;
use App\Models\Notifikasi;
use App\Models\DispenLog;

class SatpamController extends Controller
{
    public function index()
    {
        $izinList = PengajuanIzin::with(['siswa.kelas.jurusan', 'guru', 'pengaju', 'wakaApprover'])
            ->where('butuh_satpam', true)
            ->whereIn('status', ['disetujui_waka', 'pending_satpam', 'verified', 'ditolak_satpam', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        $antreanVerifikasi = $izinList->where('status', 'disetujui_waka');
        $riwayatVerifikasi = $izinList->whereIn('status', ['verified', 'ditolak_satpam', 'completed']);

        return view('satpam.dashboard', compact('izinList', 'antreanVerifikasi', 'riwayatVerifikasi'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanIzin::with([
            'siswa.kelas.jurusan',
            'guru',
            'pengaju',
            'piketApprover',
            'wakaApprover',
            'kepalaApprover',
            'satpam',
            'logs.user'
        ])->findOrFail($id);

        if (!$pengajuan->butuh_satpam) {
            return redirect()->route('satpam.dashboard')->with('error', 'Izin ini tidak memerlukan verifikasi Satpam.');
        }

        return view('satpam.show', compact('pengajuan'));
    }

    public function verifikasi(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isSatpam() && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Hanya Satpam atau Admin yang dapat memverifikasi izin gerbang.');
        }

        $request->validate([
            'status_satpam' => 'required|in:valid,tidak_valid',
            'catatan_satpam' => 'nullable|string',
        ]);

        $pengajuan = PengajuanIzin::findOrFail($id);

        if (!in_array($pengajuan->status, ['disetujui_waka', 'pending_satpam', 'completed'])) {
            return redirect()->back()->with('error', 'Pengajuan ini belum disetujui oleh Waka sehingga belum dapat diverifikasi.');
        }

        $statusSebelum = $pengajuan->status;
        $statusSesudah = ($request->status_satpam === 'valid') ? 'verified' : 'ditolak_satpam';

        $pengajuan->update([
            'status' => $statusSesudah,
            'status_satpam' => $request->status_satpam,
            'catatan_satpam' => $request->catatan_satpam,
            'tgl_satpam' => now(),
            'id_satpam' => $user->id_user,
        ]);

        // Catat log
        DispenLog::catat(
            $pengajuan->id_pengajuan,
            $user->id_user,
            $user->role,
            $statusSebelum,
            $statusSesudah,
            $request->catatan_satpam ?? ($request->status_satpam === 'valid' ? 'Identitas diverifikasi valid oleh Satpam' : 'Ditolak oleh Satpam')
        );

        $namaSubjek = $pengajuan->siswa?->nama ?? $pengajuan->guru?->nama ?? 'Siswa/Guru';

        if ($request->status_satpam === 'valid') {
            // Notifikasi ke Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Dispen Diverifikasi Satpam',
                "Pengajuan dispen {$namaSubjek} telah diverifikasi Satpam dan dinyatakan VALID.",
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'satpam'
            );

            // Notifikasi ke Pengaju
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Verifikasi Satpam Berhasil',
                "Kartu Identitas Anda telah diverifikasi oleh Satpam. Izin keluar/masuk berlaku.",
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'satpam'
            );

            return redirect()->route('satpam.dashboard')->with('success', "Verifikasi berhasil: Identitas {$namaSubjek} VALID.");
        } else {
            // Notifikasi ke Piket
            Notifikasi::kirimKeRole(
                'piket',
                'Pemeriksaan Satpam Ditolak',
                "Pemeriksaan dispen {$namaSubjek} ditolak oleh Satpam (TIDAK VALID). Catatan: " . ($request->catatan_satpam ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'satpam'
            );

            // Notifikasi ke Pengaju
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Verifikasi Satpam Ditolak',
                "Pemeriksaan identitas Anda oleh Satpam dinyatakan TIDAK VALID. Catatan: " . ($request->catatan_satpam ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'satpam'
            );

            return redirect()->route('satpam.dashboard')->with('success', "Pemeriksaan dispen dicatat sebagai TIDAK VALID.");
        }
    }
}
