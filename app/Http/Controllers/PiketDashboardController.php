<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiMasuk;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\AbsensiGuruPiket;
use Carbon\Carbon;

class PiketDashboardController extends Controller
{
    public function index()
    {
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

        // Presensi Guru Hari Ini
        $guruHadirUserIds = PresensiMasuk::where('tanggal', $todayDate)->pluck('id_user')->toArray();
        $jumlahGuruHadir = count($guruHadirUserIds);

        // Siswa Hadir & Tidak Hadir
        $jumlahSiswaHadir = AbsensiSiswa::whereDate('created_at', $todayDate)->where('status', 'hadir')->count();
        $jumlahSiswaTidakHadir = AbsensiSiswa::whereDate('created_at', $todayDate)->whereIn('status', ['izin', 'sakit', 'alpa'])->count();

        // Jadwal Hari Ini
        $jadwalHariIni = Jadwal::with(['kelas', 'guru'])
            ->where('hari', $currentDayIndo)
            ->where('aktif', 1)
            ->get();

        // Guru yang belum presensi masuk hari ini
        $guruBelumHadir = Guru::whereNotIn('id_user', $guruHadirUserIds)->get();

        // Peringatan Kelas Kosong (jadwal saat ini tapi guru belum presensi / absensi_guru_piket menyatakan tidak_hadir/kosong)
        $currentTime = Carbon::now()->format('H:i:s');
        $kelasKosong = [];
        foreach ($jadwalHariIni as $j) {
            // Check if class is ongoing right now or past start time
            if ($j->waktu_mulai && $currentTime >= $j->waktu_mulai && $currentTime <= ($j->waktu_selesai ?? '23:59:59')) {
                // Check if teacher has logged presensi masuk or piket recorded teacher absent
                $guruUser = $j->guru?->id_user;
                $hasPresensi = $guruUser ? in_array($guruUser, $guruHadirUserIds) : false;
                $piketRecord = AbsensiGuruPiket::where('id_jadwal', $j->id_jadwal)->where('tanggal', $todayDate)->first();

                if (!$hasPresensi && (!$piketRecord || $piketRecord->status_guru !== 'hadir')) {
                    $kelasKosong[] = [
                        'jadwal' => $j,
                        'pesan' => "Kelas {$j->kelas->nama_kelas} (Mapel: {$j->mapel}) belum memiliki guru yang hadir."
                    ];
                }
            }
        }

        return view('piket.dashboard', compact(
            'jumlahGuruHadir',
            'jumlahSiswaHadir',
            'jumlahSiswaTidakHadir',
            'jadwalHariIni',
            'guruBelumHadir',
            'kelasKosong'
        ));
    }
}
