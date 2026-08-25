<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanIzin;
use App\Models\DispenLog;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\AbsensiSiswa;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class WakaDashboardController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index()
    {
        $user = Auth::user();
        $isSdm = $user->isWakaSdm();

        $pengajuanQuery = PengajuanIzin::with(['siswa.kelas.jurusan', 'guru', 'pengaju', 'wakaApprover']);

        if ($isSdm) {
            $pengajuanQuery->where('kategori', 'izin_guru');
        } else {
            $pengajuanQuery->where('kategori', '!=', 'izin_guru');
        }
        if (!$user->isAdmin()) {
            $pengajuanQuery->where(function ($query) use ($user) {
                $query->where('id_waka_tujuan', $user->id_user)
                    ->orWhereNull('id_waka_tujuan');
            });
        }

        $pengajuanPending = (clone $pengajuanQuery)->whereIn('status', ['pending_waka', 'menunggu_waka'])->get();
        $pengajuanRiwayat = (clone $pengajuanQuery)->whereNotIn('status', ['pending_waka', 'menunggu_waka'])->orderBy('created_at', 'desc')->take(10)->get();

        $totalPending = $pengajuanPending->count();
        $totalDisetujui = (clone $pengajuanQuery)->whereIn('status', ['disetujui_waka', 'pending_kepala', 'menunggu_satpam', 'disetujui_kepala', 'verified', 'completed', 'selesai'])->count();
        $totalDitolak = (clone $pengajuanQuery)->where('status', 'like', 'ditolak_%')->count();

        return view('waka.dashboard', compact(
            'pengajuanPending',
            'pengajuanRiwayat',
            'isSdm',
            'totalPending',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    public function daftarPersetujuan()
    {
        $user = Auth::user();
        $isSdm = $user->isWakaSdm();

        $query = PengajuanIzin::with(['siswa.kelas.jurusan', 'guru', 'pengaju', 'wakaApprover']);

        if ($isSdm) {
            $query->where('kategori', 'izin_guru');
        } else {
            $query->where('kategori', '!=', 'izin_guru');
        }
        if (!$user->isAdmin()) {
            $query->where(function ($builder) use ($user) {
                $builder->where('id_waka_tujuan', $user->id_user)
                    ->orWhereNull('id_waka_tujuan');
            });
        }

        $pendingList = (clone $query)->whereIn('status', ['pending_waka', 'menunggu_waka'])->orderBy('created_at', 'desc')->get();
        $disetujuiList = (clone $query)->whereIn('status', ['disetujui_waka', 'pending_kepala', 'disetujui_kepala', 'verified', 'completed'])->orderBy('created_at', 'desc')->get();
        $ditolakList = (clone $query)->where('status', 'like', 'ditolak_%')->orderBy('created_at', 'desc')->get();

        return view('waka.index', compact('pendingList', 'disetujuiList', 'ditolakList', 'isSdm'));
    }

    public function show($id)
    {
        $user = Auth::user();
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

        if (!$this->canProcess($user, $pengajuan)) {
            abort(403);
        }

        $isSdm = $user->isWakaSdm();

        return view('waka.show', compact('pengajuan', 'isSdm'));
    }

    public function prosesKeputusan(Request $request, $id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanIzin::findOrFail($id);

        if (!$this->canProcess($user, $pengajuan) || !in_array($pengajuan->status, ['pending_waka', 'menunggu_waka'])) {
            abort(403);
        }

        $request->validate([
            'catatan' => 'nullable|string|required_if:keputusan,tolak',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $isGuru = ($pengajuan->kategori === 'izin_guru');
        $isPiketFlow = (bool) $pengajuan->id_waka_tujuan;
        $namaSubjek = $pengajuan->guru?->nama ?? $pengajuan->siswa?->nama ?? $pengajuan->pengaju?->nama ?? 'Siswa/Guru';

        if ($request->keputusan === 'setujui') {
            // JIKA DISPEN GURU -> Diteruskan ke Kepala Sekolah (BUKAN Satpam)
            // JIKA DISPEN SISWA -> Diteruskan ke Satpam
            $statusSesudah = $isPiketFlow
                ? 'menunggu_satpam'
                : ($isGuru ? 'pending_kepala' : 'disetujui_waka');

            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'catatan_waka' => $request->catatan,
                'alasan_penolakan' => null,
                'tgl_waka' => now(),
            ]);

            // Catat log audit
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? ($isGuru ? 'Disetujui Waka SDM & diteruskan ke Kepala Sekolah' : 'Disetujui Waka Kesiswaan & diteruskan ke Satpam')
            );

            if ($isGuru && !$isPiketFlow) {
                // Notifikasi In-App ke Kepala Sekolah
                Notifikasi::kirimKeRole(
                    'kepala_sekolah',
                    'Dispen Guru Menunggu Persetujuan Final',
                    "Pengajuan dispen guru {$namaSubjek} telah disetujui Waka SDM dan menunggu persetujuan final Kepala Sekolah.",
                    route('kepala.persetujuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );

                // Notifikasi In-App ke Pengaju
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Dispen Guru Disetujui Waka SDM',
                    "Pengajuan dispen Anda telah disetujui Waka SDM dan sedang menunggu persetujuan final Kepala Sekolah.",
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );

                // Kirim WhatsApp ke Kepala Sekolah
                $waResult = $this->waService->kirimNotifDispenKeKepala($pengajuan);
                $pesan = "Pengajuan dispen guru {$namaSubjek} BERHASIL DISETUJUI dan diteruskan ke Kepala Sekolah.";
            } else {
                // Notifikasi In-App ke Piket
                Notifikasi::kirimKeRole(
                    'piket',
                    'Dispen Disetujui Waka',
                    "Pengajuan dispen siswa {$namaSubjek} telah disetujui Waka dan diteruskan ke Satpam.",
                    route('waka.persetujuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );

                // Notifikasi In-App ke Pengaju / Ortu
                Notifikasi::kirim(
                    $pengajuan->id_user_pengaju,
                    'Pengajuan Dispen Disetujui Waka',
                    "Pengajuan dispen Anda telah disetujui Waka. Silakan periksa ke petugas Satpam saat keluar gerbang.",
                    route('pengajuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );

                // Notifikasi In-App & WhatsApp ke Satpam
                if ($pengajuan->butuh_satpam) {
                    Notifikasi::kirimKeRole(
                        'satpam',
                        'Verifikasi Dispen Baru (Acc Waka)',
                        "Dispen {$namaSubjek} telah disetujui Waka. Menunggu verifikasi fisik kartu identitas.",
                        route('satpam.show', $pengajuan->id_pengajuan),
                        'satpam'
                    );

                    $waResult = $this->waService->kirimNotifDispenKeSatpam($pengajuan);
                }

                $pesan = "Pengajuan dispensasi siswa {$namaSubjek} BERHASIL DISETUJUI dan diteruskan ke Satpam.";
            }

            if (isset($waResult) && !$waResult['success']) {
                $pesan .= ' (' . $waResult['message'] . ')';
            }

            return redirect()->route('waka.persetujuan.show', $pengajuan->id_pengajuan)->with('success', $pesan);
        } else {
            $statusSesudah = 'ditolak_waka';
            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'catatan_waka' => $request->catatan,
                'alasan_penolakan' => $request->catatan,
                'tgl_waka' => now(),
            ]);

            $waResult = $this->waService->kirimNotifPenolakanWaka($pengajuan->load(['siswa', 'guru', 'pengaju']));

            // Catat log audit
            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                $request->catatan ?? 'Ditolak oleh Waka'
            );

            // Notifikasi In-App ke Pengaju
            Notifikasi::kirim(
                $pengajuan->id_user_pengaju,
                'Pengajuan Dispen Ditolak Waka',
                "Pengajuan dispen {$namaSubjek} telah ditolak oleh Waka. Catatan: " . ($request->catatan ?? '-'),
                route('pengajuan.show', $pengajuan->id_pengajuan),
                'dispen'
            );

            return redirect()->route('waka.persetujuan.show', $pengajuan->id_pengajuan)->with('success', "Pengajuan dispensasi {$namaSubjek} telah DITOLAK.");
        }
    }

    public function monitoringSiswa(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $absensi = AbsensiSiswa::with(['siswa.kelas', 'jurnal'])
            ->whereHas('jurnal', fn ($query) => $query->where('tanggal', $tanggal))
            ->get();

        $pengajuan = PengajuanIzin::with(['siswa.kelas', 'pengaju'])
            ->whereDate('tanggal', $tanggal)
            ->where('kategori', '!=', 'izin_guru')
            ->whereNotIn('status', ['ditolak_waka', 'ditolak_kepala', 'ditolak_satpam'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ringkasan = [
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'alpa' => $absensi->where('status', 'alpa')->count(),
            'terlambat' => $absensi->filter(fn ($item) => $item->status === 'terlambat' || (int) $item->menit_terlambat > 0)->count(),
            'dispen' => $pengajuan->where('kategori', 'dispensasi')->count(),
        ];

        return view('waka.monitoring-siswa', compact('tanggal', 'absensi', 'pengajuan', 'ringkasan'));
    }

    private function canProcess(User $user, PengajuanIzin $pengajuan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($pengajuan->id_waka_tujuan && (int) $pengajuan->id_waka_tujuan !== (int) $user->id_user) {
            return false;
        }

        if ($pengajuan->id_waka_tujuan) {
            return true;
        }

        return ($user->isWakaSdm() && $pengajuan->kategori === 'izin_guru')
            || ($user->isWakaKesiswaan() && $pengajuan->kategori !== 'izin_guru');
    }
}
