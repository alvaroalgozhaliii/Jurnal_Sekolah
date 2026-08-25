<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanIzin;
use App\Models\DispenLog;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\JadwalWaka;
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

        if ($user->isWakaKurikulum()) {
            return redirect()->route('waka-kurikulum.dashboard');
        }
        if ($user->isWakaKesiswaan()) {
            return redirect()->route('waka.monitoring-siswa');
        }
        if ($user->isWakaSarpras()) {
            return redirect()->route('waka.sarpras');
        }
        if ($user->isWakaHumas()) {
            return redirect()->route('waka.humas');
        }

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

        $query = PengajuanIzin::with(['siswa.kelas.jurusan', 'guru', 'pengaju', 'wakaApprover', 'wakaTujuan']);

        if (!$user->isAdmin()) {
            $query->where(function ($builder) use ($user, $isSdm) {
                $builder->where('id_waka_tujuan', $user->id_user);
                if ($isSdm) {
                    $builder->orWhere('kategori', 'izin_guru');
                }
                if ($user->isWakaKesiswaan()) {
                    $builder->orWhere('kategori', '!=', 'izin_guru');
                }
                $builder->orWhereNull('id_waka_tujuan');
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
            'wakaTujuan',
            'kepalaApprover',
            'satpam',
            'logs.user'
        ])->findOrFail($id);

        if (!$this->canProcess($user, $pengajuan)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk memproses pengajuan ini.');
        }

        $isSdm = $user->isWakaSdm() || ($pengajuan->kategori === 'izin_guru');

        return view('waka.show', compact('pengajuan', 'isSdm'));
    }

    public function prosesKeputusan(Request $request, $id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanIzin::findOrFail($id);

        if (!$this->canProcess($user, $pengajuan) || !in_array($pengajuan->status, ['pending_waka', 'menunggu_waka'])) {
            abort(403, 'Akses ditolak atau pengajuan sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|required_if:keputusan,tolak',
            'keputusan' => 'required|in:setujui,tolak'
        ]);

        $statusSebelum = $pengajuan->status;
        $isGuru = ($pengajuan->kategori === 'izin_guru');
        $namaSubjek = $pengajuan->guru?->nama ?? $pengajuan->siswa?->nama ?? $pengajuan->pengaju?->nama ?? 'Siswa/Guru';

        if ($request->keputusan === 'setujui') {
            // JIKA DISPEN GURU -> Diteruskan ke Kepala Sekolah (BUKAN Satpam)
            // JIKA DISPEN SISWA -> Diteruskan ke Satpam
            $statusSesudah = $isGuru ? 'pending_kepala' : 'disetujui_waka';

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
                $request->catatan ?? ($isGuru ? 'Disetujui Waka & diteruskan ke Kepala Sekolah' : 'Disetujui Waka & diteruskan ke Satpam')
            );

            // Jika DISPEN GURU -> Notifikasi WhatsApp ke Kepala Sekolah
            // Jika DISPEN SISWA -> Notifikasi WhatsApp ke Satpam
            if ($isGuru) {
                $waResult = $this->waService->kirimNotifDispenKeKepala($pengajuan->load(['guru', 'pengaju', 'wakaApprover']));

                Notifikasi::kirimKeRole(
                    'kepala_sekolah',
                    'Dispen Guru Menunggu Persetujuan Final',
                    "Dispensasi guru {$namaSubjek} telah disetujui Waka dan menunggu persetujuan final Kepala Sekolah.",
                    route('kepala.persetujuan.show', $pengajuan->id_pengajuan),
                    'dispen'
                );
            } else {
                $waResult = $this->waService->kirimNotifDispenKeSatpam($pengajuan->load(['siswa.kelas', 'pengaju', 'wakaApprover']));

                Notifikasi::kirimKeRole(
                    'satpam',
                    'Dispen Siswa Disetujui Waka',
                    "Dispen siswa {$namaSubjek} telah disetujui Waka. Harap verifikasi kartu pelajar saat di gerbang.",
                    route('satpam.show', $pengajuan->id_pengajuan),
                    'dispen'
                );
            }

            // Notifikasi ke Pengaju
            if ($pengajuan->id_user_pengaju) {
                $pesanPengaju = $isGuru
                    ? "Pengajuan dispen Anda telah disetujui oleh {$user->nama} dan diteruskan ke Kepala Sekolah."
                    : "Pengajuan dispen Anda telah disetujui oleh {$user->nama}. Silakan tunjukkan ke Satpam di gerbang.";

                Notifikasi::create([
                    'id_user' => $pengajuan->id_user_pengaju,
                    'judul' => 'Pengajuan Dispen Disetujui Waka',
                    'pesan' => $pesanPengaju,
                    'link' => route('pengajuan.show', $pengajuan->id_pengajuan),
                    'tipe' => 'dispen',
                    'dibaca' => false,
                ]);
            }

            $pesanSukses = $isGuru
                ? "Pengajuan dispensasi {$namaSubjek} BERHASIL DISETUJUI dan diteruskan ke Kepala Sekolah."
                : "Pengajuan dispensasi {$namaSubjek} BERHASIL DISETUJUI dan diteruskan ke Satpam.";

            if (isset($waResult) && !$waResult['success']) {
                $pesanSukses .= ' (WhatsApp: ' . $waResult['message'] . ')';
            }

            return redirect()->route('waka.persetujuan.show', $pengajuan->id_pengajuan)->with('success', $pesanSukses);

        } else {
            // DITOLAK OLEH WAKA
            $statusSesudah = 'ditolak_waka';

            $pengajuan->update([
                'status' => $statusSesudah,
                'id_waka_approver' => $user->id_user,
                'alasan_penolakan' => $request->catatan,
                'catatan_waka' => $request->catatan,
                'tgl_waka' => now(),
            ]);

            DispenLog::catat(
                $pengajuan->id_pengajuan,
                $user->id_user,
                $user->role,
                $statusSebelum,
                $statusSesudah,
                'Ditolak Waka. Alasan: ' . ($request->catatan ?? '-')
            );

            if ($pengajuan->id_user_pengaju) {
                Notifikasi::create([
                    'id_user' => $pengajuan->id_user_pengaju,
                    'judul' => 'Pengajuan Dispen Ditolak Waka',
                    'pesan' => "Pengajuan dispen Anda ditolak oleh {$user->nama}. Alasan: " . ($request->catatan ?? '-'),
                    'link' => route('pengajuan.show', $pengajuan->id_pengajuan),
                    'tipe' => 'dispen',
                    'dibaca' => false,
                ]);
            }

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

    public function sarpras(Request $request)
    {
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $jadwalAktif = Jadwal::with(['guru', 'kelas'])->where('aktif', 1)->orderBy('hari')->orderBy('jam_ke')->get();
        
        $ruangList = $jadwalAktif->pluck('ruang')->filter()->unique()->values();

        return view('waka.sarpras', compact('kelasList', 'jadwalAktif', 'ruangList'));
    }

    public function humas(Request $request)
    {
        $dinasLuar = PengajuanIzin::with(['guru', 'pengaju', 'wakaApprover', 'kepalaApprover'])
            ->where('kategori', 'izin_guru')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('waka.humas', compact('dinasLuar'));
    }

    private function canProcess(User $user, PengajuanIzin $pengajuan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Jika user adalah Waka yang ditargetkan secara langsung
        if ($pengajuan->id_waka_tujuan && (int) $pengajuan->id_waka_tujuan === (int) $user->id_user) {
            return true;
        }

        // Jika user adalah Waka yang bertugas pada tanggal pengajuan
        $jadwal = JadwalWaka::wakaBertugasPada($pengajuan->tanggal);
        if ($jadwal && (int) $jadwal->id_user_waka === (int) $user->id_user) {
            return true;
        }

        // Waka SDM memiliki hak otorisasi untuk semua izin guru
        if ($user->isWakaSdm() && $pengajuan->kategori === 'izin_guru') {
            return true;
        }

        // Waka Kesiswaan memiliki hak otorisasi untuk semua dispen siswa
        if ($user->isWakaKesiswaan() && $pengajuan->kategori !== 'izin_guru') {
            return true;
        }

        // Jika user adalah Waka manapun dan id_waka_tujuan belum diset
        if ($user->isWaka() && is_null($pengajuan->id_waka_tujuan)) {
            return true;
        }

        return false;
    }
}
