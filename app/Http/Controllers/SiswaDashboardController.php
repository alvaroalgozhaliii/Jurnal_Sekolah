<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\AbsensiSiswa;
use Carbon\Carbon;

class SiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return view('siswa.dashboard', [
                'siswa' => null,
                'jadwalHariIni' => collect(),
                'statusPresensi' => collect(),
                'error' => 'Data profil siswa belum terhubung dengan akun ini.'
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

        // Jadwal kelas siswa hari ini
        $jadwalHariIni = Jadwal::with(['guru', 'kelas'])
            ->where('id_kelas', $siswa->id_kelas)
            ->where('hari', $currentDayIndo)
            ->where('aktif', 1)
            ->get();

        // Status presensi siswa hari ini
        $statusPresensi = AbsensiSiswa::with('jurnal')
            ->where('id_siswa', $siswa->id_siswa)
            ->whereHas('jurnal', function ($q) use ($todayDate) {
                $q->where('tanggal', $todayDate);
            })
            ->get();

        return view('siswa.dashboard', compact(
            'siswa',
            'jadwalHariIni',
            'statusPresensi'
        ));
    }

    public function jadwal()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $jadwal = Jadwal::with(['guru', 'kelas'])
            ->where('id_kelas', $siswa->id_kelas)
            ->where('aktif', 1)
            ->get()
            ->groupBy('hari');

        return view('siswa.jadwal', compact('siswa', 'jadwal'));
    }

    public function presensi()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $riwayatPresensi = AbsensiSiswa::with(['jurnal.guru', 'jurnal.jadwal'])
            ->where('id_siswa', $siswa->id_siswa)
            ->orderBy('id_absensi', 'desc')
            ->get();

        return view('siswa.presensi', compact('siswa', 'riwayatPresensi'));
    }

    public function kelas()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $kelas = $siswa->kelas ? $siswa->kelas->load('jurusan') : null;

        return view('siswa.kelas-detail', compact('siswa', 'kelas'));
    }
}
