<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\JurnalHarian;
use App\Models\Notifikasi;
use Carbon\Carbon;

class WaliKelasController extends Controller
{
    private function getKelasWali()
    {
        $user = Auth::user();
        if ($user->guru) {
            // Find class where id_guru_walikelas matches guru->id_guru or wali_kelas matches guru->nama
            $kelas = Kelas::where('id_guru_walikelas', $user->guru->id_guru)
                ->orWhere('wali_kelas', $user->guru->nama)
                ->first();
            if ($kelas) return $kelas;
        }
        return Kelas::first(); // Fallback if admin viewing
    }

    public function index()
    {
        $kelas = $this->getKelasWali();
        $totalSiswa = $kelas ? Siswa::where('id_kelas', $kelas->id_kelas)->count() : 0;

        $todayDate = Carbon::today()->toDateString();
        $presensiHariIni = collect();

        if ($kelas) {
            $siswaIds = Siswa::where('id_kelas', $kelas->id_kelas)->pluck('id_siswa');
            $presensiHariIni = AbsensiSiswa::with('siswa')
                ->whereIn('id_siswa', $siswaIds)
                ->whereHas('jurnal', function ($q) use ($todayDate) {
                    $q->where('tanggal', $todayDate);
                })
                ->get();
        }

        return view('walikelas.dashboard', compact('kelas', 'totalSiswa', 'presensiHariIni'));
    }

    public function dataKelas()
    {
        $kelas = $this->getKelasWali();
        $todayDate = Carbon::today()->toDateString();
        $siswaList = $kelas ? Siswa::with('user')->where('id_kelas', $kelas->id_kelas)->get() : collect();
        $statusHariIni = collect();

        if ($kelas) {
            $siswaIds = $siswaList->pluck('id_siswa');
            $statusHariIni = AbsensiSiswa::whereIn('id_siswa', $siswaIds)
                ->whereDate('created_at', $todayDate)
                ->get()
                ->keyBy('id_siswa');
        }

        return view('walikelas.data-kelas', compact('kelas', 'siswaList', 'statusHariIni'));
    }

    public function rekapPresensi(Request $request)
    {
        $kelas = $this->getKelasWali();
        $bulan = (int)$request->get('bulan', Carbon::now()->month);
        $tahun = (int)$request->get('tahun', Carbon::now()->year);
        $selectedSiswaId = $request->get('id_siswa');

        $siswaList = $kelas ? Siswa::where('id_kelas', $kelas->id_kelas)->get() : collect();

        $query = AbsensiSiswa::with(['siswa', 'jurnal.guru'])
            ->whereHas('jurnal', function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            });

        if ($kelas) {
            $siswaIds = $siswaList->pluck('id_siswa');
            $query->whereIn('id_siswa', $siswaIds);
        }

        if ($selectedSiswaId) {
            $query->where('id_siswa', $selectedSiswaId);
        }

        $rekapData = $query->get()->sortBy(function ($item) {
            return $item->jurnal->tanggal ?? '';
        });

        $summary = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        foreach ($rekapData as $r) {
            $st = strtolower($r->status);
            if (isset($summary[$st])) {
                $summary[$st]++;
            }
        }

        return view('walikelas.rekap-presensi', compact(
            'kelas', 'siswaList', 'bulan', 'tahun', 'selectedSiswaId', 'rekapData', 'summary'
        ));
    }

    public function jurnal()
    {
        $kelas = $this->getKelasWali();
        $jurnalList = collect();

        if ($kelas) {
            $jurnalList = JurnalHarian::with(['guru', 'jadwal'])
                ->whereHas('jadwal', function ($q) use ($kelas) {
                    $q->where('id_kelas', $kelas->id_kelas);
                })
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return view('walikelas.jurnal', compact('kelas', 'jurnalList'));
    }
}
