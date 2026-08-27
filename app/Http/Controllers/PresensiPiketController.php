<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiGuruPiket;
use App\Models\AbsensiSiswa;
use App\Models\Jadwal;
use App\Models\PresensiMasuk;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JurnalHarian;
use Carbon\Carbon;

class PresensiPiketController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $selectedDate = Carbon::parse($tanggal);
        $dayIndo = $days[$selectedDate->format('l')] ?? 'Senin';

        $query = Jadwal::with(['guru', 'kelas'])
            ->where('hari', $dayIndo)
            ->where('aktif', 1);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mapel', 'like', "%{$search}%")
                  ->orWhereHas('guru', function($qg) use ($search) {
                      $qg->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $jadwalHariIni = $query->orderBy('jam_ke', 'asc')->get();

        $absensiPiketList = AbsensiGuruPiket::where('tanggal', $tanggal)
            ->get()
            ->keyBy('id_jadwal');

        $presensiGuruMasuk = PresensiMasuk::where('tanggal', $tanggal)
            ->get()
            ->keyBy('id_user');

        return view('piket.presensi-index', compact(
            'jadwalHariIni',
            'absensiPiketList',
            'presensiGuruMasuk',
            'tanggal',
            'search'
        ));
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'tanggal' => 'required|date',
            'status_guru' => 'required|in:hadir,tidak_hadir,terlambat,digantikan',
        ]);

        AbsensiGuruPiket::updateOrCreate(
            [
                'id_jadwal' => $request->id_jadwal,
                'tanggal' => $request->tanggal,
            ],
            [
                'status_guru' => $request->status_guru,
                'pengganti' => $request->pengganti,
                'dicatat_oleh' => auth()->id(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Status kehadiran guru piket berhasil disimpan.');
    }

    // ======================================================
    // DISPEN GURU PIKET
    // ======================================================
    public function dispenGuruIndex(Request $request)
    {
        $search = $request->get('search');
        $todayDate = Carbon::today()->toDateString();

        $guruList = Guru::orderBy('nama', 'asc')->get();

        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $days[Carbon::now()->format('l')] ?? 'Senin';

        $jadwalHariIni = Jadwal::with(['kelas', 'guru'])
            ->where('hari', $currentDayIndo)
            ->where('aktif', 1)
            ->orderBy('jam_ke', 'asc')
            ->get();

        $query = AbsensiGuruPiket::with(['jadwal.guru', 'jadwal.kelas'])
            ->where('tanggal', $todayDate)
            ->whereNotNull('keperluan');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('keperluan', 'like', "%{$search}%")
                  ->orWhere('pengganti', 'like', "%{$search}%")
                  ->orWhereHas('jadwal.guru', function($qg) use ($search) {
                      $qg->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $dispenList = $query->orderBy('created_at', 'desc')->get();

        $keperluanOptions = [
            'Keperluan Dinas',
            'Rapat',
            'Tugas Sekolah',
            'Keperluan Keluarga',
            'Keperluan Kesehatan',
            'Keperluan Mendesak',
            'Lainnya'
        ];

        return view('piket.dispen-guru', compact(
            'todayDate',
            'guruList',
            'jadwalHariIni',
            'dispenList',
            'keperluanOptions',
            'search'
        ));
    }

    public function dispenGuruStore(Request $request)
    {
        $todayDate = Carbon::today()->toDateString();

        $request->validate([
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'keperluan_select' => 'required|string',
            'keperluan_custom' => 'nullable|string|max:150',
            'status_guru' => 'required|in:tidak_hadir,terlambat,digantikan',
        ]);

        $keperluan = $request->keperluan_select === 'Lainnya'
            ? ($request->keperluan_custom ?: 'Lainnya')
            : $request->keperluan_select;

        AbsensiGuruPiket::updateOrCreate(
            [
                'id_jadwal' => $request->id_jadwal,
                'tanggal' => $todayDate, // Otomatis tanggal sistem
            ],
            [
                'status_guru' => $request->status_guru,
                'keperluan' => $keperluan,
                'pengganti' => $request->pengganti,
                'dicatat_oleh' => auth()->id(),
                'created_at' => now(),
            ]
        );

        return redirect()->route('piket.dispen-guru.index')->with('success', 'Pengajuan Dispen Guru berhasil dicatat untuk tanggal hari ini (' . $todayDate . ').');
    }

    // ======================================================
    // ABSEN SISWA PIKET (BERBASIS KELAS + SEARCH)
    // ======================================================
    public function absenSiswaIndex(Request $request)
    {
        $idKelas = $request->get('id_kelas');
        $search = $request->get('search');

        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        $siswaList = collect();
        $kelasSelected = null;

        if ($idKelas) {
            $kelasSelected = Kelas::find($idKelas);
            if ($kelasSelected) {
                $query = Siswa::where('id_kelas', $idKelas);
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('nis', 'like', "%{$search}%");
                    });
                }
                $siswaList = $query->orderBy('nama', 'asc')->get();
            }
        }

        $todayDate = Carbon::today()->toDateString();
        $existingAbsensi = AbsensiSiswa::whereDate('created_at', $todayDate)
            ->get()
            ->keyBy('id_siswa');

        return view('piket.absen-siswa', compact(
            'kelasList',
            'idKelas',
            'kelasSelected',
            'siswaList',
            'search',
            'todayDate',
            'existingAbsensi'
        ));
    }

    public function absenSiswaStore(Request $request)
    {
        $request->validate([
            'absensi' => 'required|array', // [id_siswa => status]
        ]);

        $todayDate = Carbon::today()->toDateString();

        foreach ($request->absensi as $idSiswa => $status) {
            // Find or create dummy/active jurnal for today to link absensi_siswa foreign key if needed
            $siswa = Siswa::find($idSiswa);
            if (!$siswa) continue;

            $jurnal = JurnalHarian::where('tanggal', $todayDate)
                ->whereHas('jadwal', function($q) use ($siswa) {
                    $q->where('id_kelas', $siswa->id_kelas);
                })->first();

            $idJurnal = $jurnal ? $jurnal->id_jurnal : 0;

            AbsensiSiswa::updateOrCreate(
                [
                    'id_siswa' => $idSiswa,
                    'created_at' => $todayDate,
                ],
                [
                    'id_jurnal' => $idJurnal,
                    'status' => $status,
                    'dicatat_oleh' => auth()->id(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->route('piket.absen-siswa', ['id_kelas' => $request->id_kelas])->with('success', 'Absensi siswa piket berhasil disimpan.');
    }
}
