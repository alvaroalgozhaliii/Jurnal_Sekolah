<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\AbsensiSiswa;
use App\Models\PengajuanIzin;
use App\Models\Notifikasi;
use Carbon\Carbon;

class OrtuDashboardController extends Controller
{
    private function getAnakList()
    {
        $user = Auth::user();
        // Check linked children via ortu_siswa
        $anakList = $user->anakList;

        // Fallback: check direct id_user link on siswa table if pivot is empty
        if ($anakList->isEmpty()) {
            $directSiswa = Siswa::where('id_user', $user->id_user)->get();
            if ($directSiswa->isNotEmpty()) {
                $anakList = $directSiswa;
            }
        }

        return $anakList;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $anakList = $this->getAnakList();

        $selectedSiswaId = $request->get('id_siswa', $anakList->first()?->id_siswa);
        $selectedSiswa = $anakList->where('id_siswa', $selectedSiswaId)->first() ?? $anakList->first();

        $todayDate = Carbon::today()->toDateString();
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $days[Carbon::now()->format('l')] ?? 'Senin';

        $jadwalHariIni = collect();
        $statusPresensi = collect();
        $summaryAnak = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'dispen' => 0];

        if ($selectedSiswa) {
            $jadwalHariIni = Jadwal::with(['guru', 'kelas'])
                ->where('id_kelas', $selectedSiswa->id_kelas)
                ->where('hari', $currentDayIndo)
                ->where('aktif', 1)
                ->get();

            $statusPresensi = AbsensiSiswa::with('jurnal')
                ->where('id_siswa', $selectedSiswa->id_siswa)
                ->whereHas('jurnal', function ($q) use ($todayDate) {
                    $q->where('tanggal', $todayDate);
                })
                ->get();

            // Summary presensi anak untuk Grafik Analytics
            $allAbsensi = AbsensiSiswa::where('id_siswa', $selectedSiswa->id_siswa)->get();
            foreach ($allAbsensi as $ab) {
                $st = strtolower($ab->status);
                if (isset($summaryAnak[$st])) {
                    $summaryAnak[$st]++;
                }
            }
            $summaryAnak['dispen'] = PengajuanIzin::where('id_siswa', $selectedSiswa->id_siswa)
                ->where('kategori', 'dispensasi')
                ->where('status', 'completed')
                ->count();
        }

        $notifikasiList = Notifikasi::where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('ortu.dashboard', compact(
            'anakList',
            'selectedSiswa',
            'jadwalHariIni',
            'statusPresensi',
            'summaryAnak',
            'notifikasiList'
        ));
    }

    public function dataAnak(Request $request)
    {
        $anakList = $this->getAnakList();
        $selectedSiswaId = $request->get('id_siswa', $anakList->first()?->id_siswa);
        $selectedSiswa = $anakList->where('id_siswa', $selectedSiswaId)->first() ?? $anakList->first();

        if ($selectedSiswa) {
            $selectedSiswa->load(['kelas.jurusan']);
        }

        return view('ortu.data-anak', compact('anakList', 'selectedSiswa'));
    }

    public function jadwal(Request $request)
    {
        $anakList = $this->getAnakList();
        $selectedSiswaId = $request->get('id_siswa', $anakList->first()?->id_siswa);
        $selectedSiswa = $anakList->where('id_siswa', $selectedSiswaId)->first() ?? $anakList->first();

        $jadwal = collect();
        if ($selectedSiswa) {
            $jadwal = Jadwal::with(['guru', 'kelas'])
                ->where('id_kelas', $selectedSiswa->id_kelas)
                ->where('aktif', 1)
                ->get()
                ->groupBy('hari');
        }

        return view('ortu.jadwal', compact('anakList', 'selectedSiswa', 'jadwal'));
    }

    public function presensi(Request $request)
    {
        $anakList = $this->getAnakList();
        $selectedSiswaId = $request->get('id_siswa', $anakList->first()?->id_siswa);
        $selectedSiswa = $anakList->where('id_siswa', $selectedSiswaId)->first() ?? $anakList->first();

        $riwayatPresensi = collect();
        if ($selectedSiswa) {
            $riwayatPresensi = AbsensiSiswa::with(['jurnal.guru', 'jurnal.jadwal'])
                ->where('id_siswa', $selectedSiswa->id_siswa)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('ortu.presensi', compact('anakList', 'selectedSiswa', 'riwayatPresensi'));
    }

    public function rekapBulanan(Request $request)
    {
        $anakList = $this->getAnakList();
        $selectedSiswaId = $request->get('id_siswa', $anakList->first()?->id_siswa);
        $selectedSiswa = $anakList->where('id_siswa', $selectedSiswaId)->first() ?? $anakList->first();

        $bulan = (int)$request->get('bulan', Carbon::now()->month);
        $tahun = (int)$request->get('tahun', Carbon::now()->year);

        $rekapData = collect();
        $summary = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        if ($selectedSiswa) {
            $rekapData = AbsensiSiswa::with(['jurnal.guru'])
                ->where('id_siswa', $selectedSiswa->id_siswa)
                ->whereHas('jurnal', function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun);
                })
                ->get()
                ->sortBy(function ($item) {
                    return $item->jurnal->tanggal ?? '';
                });

            foreach ($rekapData as $r) {
                $st = strtolower($r->status);
                if (isset($summary[$st])) {
                    $summary[$st]++;
                }
            }
        }

        return view('ortu.rekap-bulanan', compact(
            'anakList', 'selectedSiswa', 'bulan', 'tahun', 'rekapData', 'summary'
        ));
    }

    public function notifikasi()
    {
        $user = Auth::user();
        $notifikasi = Notifikasi::where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark all as read
        Notifikasi::where('id_user', $user->id_user)->update(['dibaca' => true]);

        return view('ortu.notifikasi', compact('notifikasi'));
    }

    public function pesan()
    {
        $user = Auth::user();
        $pesanList = Notifikasi::where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ortu.pesan', compact('pesanList'));
    }
}
