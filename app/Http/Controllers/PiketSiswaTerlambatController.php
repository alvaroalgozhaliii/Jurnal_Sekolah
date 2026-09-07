<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SiswaTerlambat;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\JurnalHarian;
use App\Models\AbsensiSiswa;
use App\Models\Notifikasi;
use App\Services\KbmService;
use Carbon\Carbon;

class PiketSiswaTerlambatController extends Controller
{
    private function getIndoDay($date)
    {
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        return $days[Carbon::parse($date)->format('l')] ?? 'Senin';
    }

    public function index(Request $request)
    {
        $tanggal  = $request->get('tanggal', Carbon::today()->toDateString());
        $idKelas  = $request->get('id_kelas');
        $search   = $request->get('search');

        $query = SiswaTerlambat::with(['siswa.kelas', 'kelas', 'petugasPiket']);

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }
        if ($idKelas) {
            $query->where('id_kelas', $idKelas);
        }
        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $terlambatList = $query->orderBy('created_at', 'desc')->get();
        $kelasList     = Kelas::orderBy('nama_kelas', 'asc')->get();
        $totalHariIni  = SiswaTerlambat::where('tanggal', Carbon::today()->toDateString())->count();

        return view('piket.siswa-terlambat.index', compact(
            'terlambatList', 'kelasList', 'tanggal', 'idKelas', 'search', 'totalHariIni'
        ));
    }

    public function create()
    {
        $siswaList   = Siswa::with('kelas')->where('aktif', 1)->orderBy('nama', 'asc')->get();
        $todayDate   = Carbon::today()->toDateString();
        $hariIni     = $this->getIndoDay($todayDate);
        $now         = Carbon::now();
        $currentSlot = KbmService::getCurrentSlotInfo($now);

        // Bangun opsi jam pelajaran dari KbmService sesuai hari ini
        $jamOptions = [];
        $daftarJam  = KbmService::getDaftarJamKe($hariIni);
        foreach ($daftarJam as $jamKe => $label) {
            $alokasi = KbmService::getAlokasiWaktu($hariIni, (int)$jamKe);
            if ($alokasi) {
                $jamOptions[] = [
                    'jam_ke'        => $jamKe,
                    'waktu_mulai'   => $alokasi['waktu_mulai'],
                    'waktu_selesai' => $alokasi['waktu_selesai'],
                ];
            }
        }

        return view('piket.siswa-terlambat.create', compact(
            'siswaList', 'todayDate', 'hariIni', 'jamOptions', 'now', 'currentSlot'
        ));
    }

    public function store(Request $request)
    {
        $now = Carbon::now();
        $currentSlot = KbmService::getCurrentSlotInfo($now);
        $detectedJamKe = $currentSlot['jam_ke'] ?? 1;
        $detectedWaktu = $currentSlot['waktu_mulai'] ?? '07:00';

        $request->merge([
            'jam_ke_dipilih' => $request->get('jam_ke_dipilih') ?: $detectedJamKe,
            'jam_kedatangan' => $request->get('jam_kedatangan') ?: $detectedWaktu,
        ]);

        $request->validate([
            'id_siswa'       => 'required|exists:siswa,id_siswa',
            'tanggal'        => 'required|date',
            'jam_ke_dipilih' => 'required|integer|min:1|max:13',
            'jam_kedatangan' => 'required',
            'alasan'         => 'required|string|max:500',
            'tindakan_piket' => 'nullable|string|max:500',
        ]);

        $siswa      = Siswa::with('kelas.guruWaliKelas.user')->findOrFail($request->id_siswa);
        $kelas      = $siswa->kelas;
        $tanggal    = $request->tanggal;
        $sampaiJam  = (int) $request->jam_ke_dipilih;
        $jamKedatangan = $request->jam_kedatangan; // waktu_mulai dari jam yang dipilih
        $alasan     = $request->alasan;
        $tindakan   = $request->tindakan_piket ?: 'Diberikan surat izin masuk kelas';
        $hariIndo   = $this->getIndoDay($tanggal);

        // 1. Simpan catatan keterlambatan
        $logTerlambat = SiswaTerlambat::create([
            'id_siswa'             => $siswa->id_siswa,
            'id_kelas'             => $siswa->id_kelas,
            'tanggal'              => $tanggal,
            'jam_kedatangan'       => $jamKedatangan,
            'terlambat_sampai_jam' => $sampaiJam,
            'alasan'               => $alasan,
            'tindakan_piket'       => $tindakan,
            'id_petugas_piket'     => Auth::id(),
            'status_notifikasi'    => true,
        ]);

        // 2. Cari jadwal KBM kelas siswa pada hari bersangkutan
        $jadwals = collect();
        if ($kelas) {
            $jadwals = Jadwal::with(['guru.user'])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('hari', $hariIndo)
                ->where('aktif', 1)
                ->get();
        }

        $guruNotifiedIds = [];

        foreach ($jadwals as $jadwal) {
            // Cek apakah jam_ke jadwal ini termasuk yang dilewati keterlambatan
            $isAffected = false;
            if (preg_match_all('/\d+/', $jadwal->jam_ke, $matches)) {
                foreach ($matches[0] as $jNum) {
                    if ((int)$jNum <= $sampaiJam) {
                        $isAffected = true;
                        break;
                    }
                }
            } else {
                if ((int)$jadwal->jam_ke <= $sampaiJam) {
                    $isAffected = true;
                }
            }

            if ($isAffected) {
                // Cari Jurnal Harian & update Absensi Siswa
                $jurnal = JurnalHarian::where('id_jadwal', $jadwal->id_jadwal)
                    ->where('tanggal', $tanggal)
                    ->first();

                if ($jurnal) {
                    AbsensiSiswa::updateOrCreate(
                        [
                            'id_jurnal' => $jurnal->id_jurnal,
                            'id_siswa'  => $siswa->id_siswa,
                        ],
                        [
                            'status'     => 'terlambat',
                            'jam_masuk'  => $jamKedatangan,
                            'keterangan' => 'Terlambat s.d Jam ke-' . $sampaiJam . ' (Piket: ' . $alasan . ')',
                            'dicatat_oleh' => Auth::id(),
                            'created_at' => now(),
                        ]
                    );
                }

                // Notifikasi guru pengajar di jam terdampak
                if ($jadwal->guru && $jadwal->guru->user && !in_array($jadwal->guru->user->id_user, $guruNotifiedIds)) {
                    $guruUser          = $jadwal->guru->user;
                    $guruNotifiedIds[] = $guruUser->id_user;

                    Notifikasi::kirim(
                        $guruUser->id_user,
                        'Pemberitahuan Siswa Terlambat',
                        "Siswa {$siswa->nama} ({$kelas->nama_kelas}) terlambat tiba pukul {$jamKedatangan} (sampai Jam Pelajaran ke-{$sampaiJam}) pada jam mengajar Anda ({$jadwal->mapel}). Alasan: {$alasan}. Status presensi siswa otomatis disesuaikan menjadi Terlambat.",
                        route('guru.dashboard'),
                        'terlambat'
                    );
                }
            }
        }

        // 3. Notifikasi wali kelas
        if ($kelas && $kelas->guruWaliKelas && $kelas->guruWaliKelas->user) {
            $waliUser = $kelas->guruWaliKelas->user;
            Notifikasi::kirim(
                $waliUser->id_user,
                'Laporan Siswa Terlambat',
                "Siswa kelas Anda, {$siswa->nama} ({$kelas->nama_kelas}) tercatat terlambat hari ini pukul {$jamKedatangan} (sampai Jam ke-{$sampaiJam}). Alasan: {$alasan}.",
                route('walikelas.siswa-terlambat'),
                'terlambat'
            );
        }

        return redirect()->route('piket.siswa-terlambat.index')
            ->with('success', "Keterlambatan {$siswa->nama} berhasil dicatat. Status absensi telah disesuaikan dan notifikasi terkirim ke guru pengajar jam 1–{$sampaiJam} serta wali kelas.")
            ->with('slip_id', $logTerlambat->id_terlambat);
    }

    public function cetakSlip($id)
    {
        $terlambat = SiswaTerlambat::with(['siswa.kelas', 'kelas', 'petugasPiket'])->findOrFail($id);
        return view('piket.siswa-terlambat.slip', compact('terlambat'));
    }

    public function destroy($id)
    {
        $terlambat = SiswaTerlambat::findOrFail($id);
        $terlambat->delete();
        return back()->with('success', 'Catatan siswa terlambat berhasil dihapus.');
    }
}
