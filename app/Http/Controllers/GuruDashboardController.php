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
use App\Models\JadwalWaka;
use App\Models\Notifikasi;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeAccess = session('active_access');

        // Jika user sedang aktif dengan peran penugasan lain (misal waka atau wali_kelas), arahkan ke dashboard peran tersebut
        if ($activeAccess && $activeAccess !== 'guru' && !$user->isAdmin()) {
            $routeName = AuthController::getDashboardRouteName($activeAccess);
            if (\Illuminate\Support\Facades\Route::has($routeName) && $routeName !== 'guru.dashboard') {
                return redirect()->route($routeName);
            }
        }

        $guru = $user->guru;

        if (!$guru) {
            $guru = \App\Models\Guru::where('nama', $user->nama)
                ->orWhere('nip', $user->nip)
                ->first();
        }

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
            ->orderBy('jam_ke', 'asc')
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

        // Cek status pengisian per jadwal & pengelompokan blok sesi mengajar
        // Jika salah satu jam dalam blok kelas & mapel yang sama sudah diisi, semua jam dalam blok itu dianggap sudah diisi
        $blockFilledStatus = [];
        $blockJurnalMap = [];
        foreach ($jadwalHariIni as $j) {
            $blockKey = $j->id_kelas . '_' . $j->mapel;
            if ($jurnalHariIni->has($j->id_jadwal)) {
                $blockFilledStatus[$blockKey] = true;
                $blockJurnalMap[$blockKey] = $jurnalHariIni->get($j->id_jadwal);
            }
        }

        // Pengingat Jurnal (dikelompokkan per sesi kelas & mapel)
        $pengingatJurnal = [];
        $checkedBlocks = [];
        foreach ($jadwalHariIni as $j) {
            $blockKey = $j->id_kelas . '_' . $j->mapel;
            $isFilled = !empty($blockFilledStatus[$blockKey]);

            if (!$isFilled && !in_array($blockKey, $checkedBlocks)) {
                $checkedBlocks[] = $blockKey;
                $companionJams = $jadwalHariIni->where('id_kelas', $j->id_kelas)->where('mapel', $j->mapel)->pluck('jam_ke')->sort()->implode(', ');
                $namaKelas = $j->kelas?->nama_kelas ?? 'Kelas -';
                $pengingatJurnal[] = "Anda belum mengisi jurnal untuk kelas {$namaKelas} (Mapel: {$j->mapel}, Jam ke-{$companionJams}).";
            }
        }

        // Monitoring Siswa Kelas (Penugasan Wali Kelas)
        // Penugasan Wali Kelas dipisahkan ke Portal / Dashboard Wali Kelas (walikelas.dashboard).
        // Dashboard Guru sekarang murni hanya menampilkan aktivitas KBM & pembelajaran.
        $waliMonitoring = null;

        // Data Petugas Piket Hari Ini
        $piketHariIni = JadwalWaka::with(['waka', 'guruPiket'])->whereDate('tanggal', $todayDate)->first();
        $isSayaPiketHariIni = false;
        if ($piketHariIni && $guru) {
            $isSayaPiketHariIni = $piketHariIni->isGuruBertugas($guru);
        }

        // Jika guru ini bertugas piket hari ini, buat notifikasi di sistem jika belum dibuat hari ini
        if ($isSayaPiketHariIni && $user) {
            $notifPiketAda = Notifikasi::where('id_user', $user->id_user)
                ->where('type', 'piket_duty')
                ->whereDate('created_at', $todayDate)
                ->exists();

            if (!$notifPiketAda) {
                Notifikasi::kirim(
                    $user->id_user,
                    '📋 Jadwal Tugas Guru Piket Hari Ini',
                    'Bapak/Ibu ' . ($guru->nama ?? $user->nama) . ', Anda terdaftar bertugas sebagai Petugas Piket hari ini (' . Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') . '). Silakan pantau kehadiran siswa dan tertib sekolah.',
                    route('piket.dashboard'),
                    'piket_duty'
                );
            }
        }

        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'jurnalHariIni',
            'blockFilledStatus',
            'blockJurnalMap',
            'presensiHariIni',
            'pengingatJurnal',
            'jurnalTerlaksana',
            'jurnalPengganti',
            'jurnalTidakTerlaksana',
            'totalJurnalGuru',
            'waliMonitoring',
            'piketHariIni',
            'isSayaPiketHariIni'
        ));
    }
}
