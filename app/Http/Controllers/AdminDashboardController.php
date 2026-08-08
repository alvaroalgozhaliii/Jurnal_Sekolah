<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Jadwal;
use App\Models\JurnalHarian;
use App\Models\PresensiMasuk;
use App\Models\AbsensiSiswa;
use App\Models\TahunPelajaran;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $jumlahGuru = Guru::count();
        $jumlahSiswa = Siswa::count();
        $jumlahKelas = Kelas::count();
        $jumlahJurusan = Jurusan::count();
        $jumlahMapel = Jadwal::distinct('mapel')->count('mapel');

        $kehadiranGuruHariIni = PresensiMasuk::where('tanggal', $today)->count();
        $kehadiranSiswaHariIni = AbsensiSiswa::whereDate('created_at', $today)
            ->where('status', 'hadir')
            ->count();
        $jurnalHariIni = JurnalHarian::where('tanggal', $today)->count();

        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        return view('admin.dashboard', compact(
            'jumlahGuru',
            'jumlahSiswa',
            'jumlahKelas',
            'jumlahJurusan',
            'jumlahMapel',
            'kehadiranGuruHariIni',
            'kehadiranSiswaHariIni',
            'jurnalHariIni',
            'tahunAktif'
        ));
    }

    public function rekapKehadiran(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId = $request->get('id_kelas');
        $guruId = $request->get('id_guru');

        $kelas = Kelas::all();
        $guru = Guru::all();

        // Rekap Siswa query
        $querySiswa = AbsensiSiswa::with(['siswa', 'jurnal'])
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->where('tanggal', $tanggal);
            });

        if ($kelasId) {
            $querySiswa->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            });
        }
        $rekapSiswa = $querySiswa->get();

        // Rekap Guru query
        $queryGuru = PresensiMasuk::with('user.guru')
            ->where('tanggal', $tanggal);

        if ($guruId) {
            $queryGuru->whereHas('user.guru', function ($q) use ($guruId) {
                $q->where('id_guru', $guruId);
            });
        }
        $rekapGuru = $queryGuru->get();

        return view('admin.rekap-kehadiran', compact(
            'rekapSiswa',
            'rekapGuru',
            'kelas',
            'guru',
            'tanggal',
            'kelasId',
            'guruId'
        ));
    }
}
