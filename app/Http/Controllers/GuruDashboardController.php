<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\JurnalHarian;
use App\Models\PresensiMasuk;
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

        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'jurnalHariIni',
            'presensiHariIni',
            'pengingatJurnal',
            'jurnalTerlaksana',
            'jurnalPengganti',
            'jurnalTidakTerlaksana',
            'totalJurnalGuru'
        ));
    }
}
