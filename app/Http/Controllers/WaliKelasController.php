<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\JurnalHarian;
use App\Models\SiswaTerlambat;
use App\Models\Notifikasi;
use Carbon\Carbon;

class WaliKelasController extends Controller
{
    private function getKelasWali()
    {
        $user = Auth::user();
        if ($user->guru) {
            $kelas = Kelas::where('id_guru_walikelas', $user->guru->id_guru)
                ->orWhere('wali_kelas', $user->guru->nama)
                ->first();
            if ($kelas) return $kelas;
        }
        return Kelas::first(); // Fallback if admin viewing
    }

    public function index()
    {
        $kelas      = $this->getKelasWali();
        $totalSiswa = $kelas ? Siswa::where('id_kelas', $kelas->id_kelas)->count() : 0;

        $todayDate      = Carbon::today()->toDateString();
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

    /**
     * Data Kelas — support $tanggal parameter for date-specific attendance status
     */
    public function dataKelas(Request $request)
    {
        $kelas     = $this->getKelasWali();
        $today     = Carbon::today()->toDateString();
        $tanggal   = $request->get('tanggal', $today);
        $siswaList = $kelas
            ? Siswa::with('user')->where('id_kelas', $kelas->id_kelas)->get()
            : collect();

        $statusTanggal = collect();

        if ($kelas) {
            $siswaIds      = $siswaList->pluck('id_siswa');
            $statusTanggal = AbsensiSiswa::whereIn('id_siswa', $siswaIds)
                ->whereHas('jurnal', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal);
                })
                ->get()
                ->keyBy('id_siswa');
        }

        // Also keep backward-compat variable name
        $statusHariIni = $statusTanggal;

        return view('walikelas.data-kelas', compact(
            'kelas', 'siswaList', 'statusTanggal', 'statusHariIni', 'tanggal', 'today'
        ));
    }

    /**
     * Rekap Presensi — bulanan, support bulan/tahun navigation
     */
    public function rekapPresensi(Request $request)
    {
        $kelas           = $this->getKelasWali();
        $bulan           = (int) $request->get('bulan', Carbon::now()->month);
        $tahun           = (int) $request->get('tahun', Carbon::now()->year);
        $selectedSiswaId = $request->get('id_siswa');

        $siswaList = $kelas
            ? Siswa::where('id_kelas', $kelas->id_kelas)->get()
            : collect();

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

        $rekapData = $query->get()->sortBy(fn($item) => $item->jurnal->tanggal ?? '');

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

    /**
     * Jurnal — support $tanggal parameter to filter by specific date
     */
    public function jurnal(Request $request)
    {
        $kelas   = $this->getKelasWali();
        $tanggal = $request->get('tanggal'); // null = show all

        $jurnalAll  = collect();
        $jurnalList = collect();

        if ($kelas) {
            // All journals for dot indicators
            $jurnalAll = JurnalHarian::with(['guru', 'jadwal'])
                ->whereHas('jadwal', function ($q) use ($kelas) {
                    $q->where('id_kelas', $kelas->id_kelas);
                })
                ->orderBy('tanggal', 'desc')
                ->get();

            // Filtered list
            $query = JurnalHarian::with(['guru', 'jadwal'])
                ->whereHas('jadwal', function ($q) use ($kelas) {
                    $q->where('id_kelas', $kelas->id_kelas);
                })
                ->orderBy('tanggal', 'desc');

            if ($tanggal) {
                $query->where('tanggal', $tanggal);
            }

            $jurnalList = $query->get();
        }

        $today   = Carbon::today()->toDateString();
        $selDate = $tanggal;
        $selC    = $selDate
            ? Carbon::parse($selDate)
            : Carbon::today();
        $navYear  = $selC->year;
        $navMonth = $selC->month;

        return view('walikelas.jurnal', compact(
            'kelas', 'jurnalList', 'jurnalAll', 'tanggal', 'today', 'selDate', 'navYear', 'navMonth'
        ));
    }

    /**
     * Siswa Terlambat — single date (klik kalender), plus all-month for dots & summary
     */
    public function siswaTerlambat(Request $request)
    {
        $kelas           = $this->getKelasWali();
        $today           = Carbon::today()->toDateString();
        $tanggal         = $request->get('tanggal', $today);
        $selectedSiswaId = $request->get('id_siswa');

        $siswaList = $kelas
            ? Siswa::where('id_kelas', $kelas->id_kelas)->orderBy('nama')->get()
            : collect();

        // All data in the same month → for summary & dot indicators
        $selC      = Carbon::parse($tanggal);
        $calYear   = $selC->year;
        $calMonth  = $selC->month;
        $startMonth = $selC->copy()->startOfMonth()->toDateString();
        $endMonth   = $selC->copy()->endOfMonth()->toDateString();

        $queryAll = SiswaTerlambat::with(['siswa', 'petugasPiket'])
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->orderBy('tanggal', 'desc');

        if ($kelas) {
            $siswaIds = $siswaList->pluck('id_siswa');
            $queryAll->whereIn('id_siswa', $siswaIds);
        }

        if ($selectedSiswaId) {
            $queryAll->where('id_siswa', $selectedSiswaId);
        }

        $terlambatListAll = $queryAll->get();

        // Filtered by selected date
        $queryDay = SiswaTerlambat::with(['siswa', 'kelas', 'petugasPiket'])
            ->where('tanggal', $tanggal)
            ->orderBy('created_at', 'desc');

        if ($kelas) {
            $siswaIds = $siswaList->pluck('id_siswa');
            $queryDay->whereIn('id_siswa', $siswaIds);
        }

        if ($selectedSiswaId) {
            $queryDay->where('id_siswa', $selectedSiswaId);
        }

        $terlambatList = $queryDay->get();

        // Backward compat (export uses $dari/$sampai)
        $dari   = $startMonth;
        $sampai = $endMonth;

        return view('walikelas.siswa-terlambat', compact(
            'kelas', 'siswaList', 'tanggal', 'selectedSiswaId',
            'terlambatList', 'terlambatListAll',
            'dari', 'sampai', 'today', 'selC', 'calYear', 'calMonth'
        ));
    }

    public function exportSiswaTerlambatCsv(Request $request)
    {
        $kelas = $this->getKelasWali();
        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Support both old range params and new single-date
        $tanggal = $request->get('tanggal');
        if ($tanggal) {
            $dari   = $tanggal;
            $sampai = $tanggal;
        } else {
            $dari   = $request->get('dari', Carbon::now()->startOfMonth()->toDateString());
            $sampai = $request->get('sampai', Carbon::today()->toDateString());
        }

        $selectedSiswaId = $request->get('id_siswa');
        $siswaIds        = Siswa::where('id_kelas', $kelas->id_kelas)->pluck('id_siswa');

        $query = SiswaTerlambat::with(['siswa', 'petugasPiket'])
            ->whereIn('id_siswa', $siswaIds)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'asc');

        if ($selectedSiswaId) {
            $query->where('id_siswa', $selectedSiswaId);
        }

        $data     = $query->get();
        $filename = "siswa_terlambat_{$kelas->nama_kelas}_{$dari}_{$sampai}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data, $kelas, $dari, $sampai) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ["Rekap Siswa Terlambat — Kelas {$kelas->nama_kelas} ({$dari} s.d {$sampai})"]);
            fputcsv($out, []);
            fputcsv($out, ['No', 'Tanggal', 'Hari', 'Nama Siswa', 'NISN', 'Terlambat s.d Jam', 'Alasan', 'Tindakan Piket', 'Dicatat Oleh']);

            foreach ($data as $i => $t) {
                $hari = Carbon::parse($t->tanggal)->locale('id')->isoFormat('dddd');
                fputcsv($out, [
                    $i + 1,
                    $t->tanggal,
                    $hari,
                    $t->siswa->nama ?? '-',
                    $t->siswa->NISN ?? '-',
                    'Jam ke-' . $t->terlambat_sampai_jam,
                    $t->alasan,
                    $t->tindakan_piket ?? '-',
                    $t->petugasPiket->nama ?? '-',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function exportRekapCsv(Request $request)
    {
        $kelas = $this->getKelasWali();
        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $bulan           = (int) $request->get('bulan', Carbon::now()->month);
        $tahun           = (int) $request->get('tahun', Carbon::now()->year);
        $selectedSiswaId = $request->get('id_siswa');

        $siswaList = Siswa::where('id_kelas', $kelas->id_kelas)->get();
        $siswaIds  = $siswaList->pluck('id_siswa');

        $query = AbsensiSiswa::with(['siswa', 'jurnal'])
            ->whereIn('id_siswa', $siswaIds)
            ->whereHas('jurnal', fn($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun));

        if ($selectedSiswaId) {
            $query->where('id_siswa', $selectedSiswaId);
        }

        $rekapData = $query->get()->sortBy(fn($r) => $r->jurnal->tanggal ?? '');
        $bulanStr  = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
        $filename  = "rekap_presensi_{$kelas->nama_kelas}_{$tahun}_{$bulan}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rekapData, $kelas, $bulanStr) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ["Rekap Presensi — Kelas {$kelas->nama_kelas} — {$bulanStr}"]);
            fputcsv($out, []);
            fputcsv($out, ['No', 'Tanggal', 'Hari', 'NISN', 'Nama Siswa', 'Status', 'Jam Masuk', 'Keterlambatan (menit)', 'Keterangan']);

            foreach ($rekapData as $i => $r) {
                $tgl  = $r->jurnal->tanggal ?? '';
                $hari = $tgl ? Carbon::parse($tgl)->locale('id')->isoFormat('dddd') : '-';
                fputcsv($out, [
                    $i + 1,
                    $tgl ? Carbon::parse($tgl)->format('d/m/Y') : '-',
                    $hari,
                    $r->siswa->NISN ?? '-',
                    $r->siswa->nama ?? '-',
                    strtoupper($r->status),
                    $r->jam_masuk ?? '-',
                    $r->menit_terlambat ?? '-',
                    $r->keterangan ?? '-',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
