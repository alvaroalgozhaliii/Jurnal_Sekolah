<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\JurnalHarian;
use App\Models\PresensiMasuk;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\SiswaTerlambat;
use App\Models\PengajuanIzin;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return view('guru.dashboard', [
                'jadwalHariIni' => collect(),
                'jurnalHariIni' => collect(),
                'presensiHariIni' => null,
                'pengingatJurnal' => [],
                'waliMonitoring' => null,
                'error' => 'Data profil guru belum terhubung dengan akun ini.'
            ]);
        }

        $todayDate = Carbon::today()->toDateString();
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $days[Carbon::now()->format('l')] ?? 'Senin';

        // Jadwal hari ini untuk guru ini
        $jadwalHariIni = Jadwal::with('kelas')
            ->where('id_guru', $guru->id_guru)
            ->where('hari', $currentDayIndo)
            ->where('aktif', 1)
            ->get();

        // Jurnal hari ini yang sudah diisi
        $jurnalHariIni = JurnalHarian::where('id_guru', $guru->id_guru)
            ->where('tanggal', $todayDate)
            ->get()
            ->keyBy('id_jadwal');

        // Status presensi guru hari ini
        $presensiHariIni = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        // Statistik Mengajar Guru untuk Grafik Analytics
        $allJurnalGuru = JurnalHarian::where('id_guru', $guru->id_guru)->get();
        $jurnalTerlaksana = $allJurnalGuru->where('status_keterlaksanaan', 'terlaksana')->count();
        $jurnalPengganti = $allJurnalGuru->where('status_keterlaksanaan', 'pengganti')->count();
        $jurnalTidakTerlaksana = $allJurnalGuru->whereIn('status_keterlaksanaan', ['tidak_terlaksana', 'kosong'])->count();
        $totalJurnalGuru = $allJurnalGuru->count();

        // Pengingat Jurnal
        $pengingatJurnal = [];
        foreach ($jadwalHariIni as $j) {
            if (!$jurnalHariIni->has($j->id_jadwal)) {
                $pengingatJurnal[] = "Anda belum mengisi jurnal untuk kelas {$j->kelas->nama_kelas} (Mapel: {$j->mapel}, Jam ke-{$j->jam_ke}).";
            }
        }

        // Monitoring Siswa Kelas (Jika Guru ditugaskan sebagai Wali Kelas)
        $kelasWali = $user->kelas_wali;
        $waliMonitoring = null;

        if ($kelasWali) {
            $siswaWali = Siswa::where('id_kelas', $kelasWali->id_kelas)->get();
            $totalSiswaWali = $siswaWali->count();
            $siswaIds = $siswaWali->pluck('id_siswa');

            // Presensi hari ini di kelas bimbingan
            $presensiHariIniKelas = AbsensiSiswa::with('siswa')
                ->whereIn('id_siswa', $siswaIds)
                ->whereHas('jurnal', function ($q) use ($todayDate) {
                    $q->where('tanggal', $todayDate);
                })
                ->get();

            $hadirCount = $presensiHariIniKelas->where('status', 'hadir')->count();
            $sakitCount = $presensiHariIniKelas->where('status', 'sakit')->count();
            $izinCount = $presensiHariIniKelas->where('status', 'izin')->count();
            $alpaCount = $presensiHariIniKelas->where('status', 'alpa')->count();
            $terlambatCount = $presensiHariIniKelas->where('status', 'terlambat')->count();

            // Log siswa terlambat hari ini yang dicatat piket
            $terlambatHariIni = SiswaTerlambat::with('siswa')
                ->whereIn('id_siswa', $siswaIds)
                ->where('tanggal', $todayDate)
                ->orderBy('jam_kedatangan', 'desc')
                ->get();

            // Pengajuan izin / sakit siswa kelas ini hari ini
            $izinHariIni = PengajuanIzin::with('siswa')
                ->whereIn('id_siswa', $siswaIds)
                ->where('tanggal', $todayDate)
                ->get();

            $waliMonitoring = [
                'kelas' => $kelasWali,
                'totalSiswa' => $totalSiswaWali,
                'hadir' => $hadirCount,
                'sakit' => $sakitCount,
                'izin' => $izinCount,
                'alpa' => $alpaCount,
                'terlambat' => $terlambatCount,
                'siswaTerlambatList' => $terlambatHariIni,
                'siswaIzinList' => $izinHariIni,
            ];
        }

        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'jurnalHariIni',
            'presensiHariIni',
            'pengingatJurnal',
            'jurnalTerlaksana',
            'jurnalPengganti',
            'jurnalTidakTerlaksana',
            'totalJurnalGuru',
            'waliMonitoring'
        ));
    }
}
