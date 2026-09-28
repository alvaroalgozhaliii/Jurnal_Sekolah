<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Models\TahunPelajaran;
use App\Models\Notifikasi;
use Carbon\Carbon;

use App\Services\KbmService;

class PengaturanController extends Controller
{
    public function index()
    {
        return redirect()->route('profil.show', ['tab' => 'pengaturan']);
    }

    /**
     * Tampilan Khusus Pengaturan Jam Sekolah (Admin)
     */
    public function jamSekolahIndex()
    {
        $todayDate = Carbon::today(config('app.timezone', 'Asia/Jakarta'))->toDateString();

        $jamMasuk = KbmService::getJamMasuk();
        $jamPulang = KbmService::getJamPulang('Senin');
        $jamPulangJumatX = KbmService::getJamPulangJumatX();
        $jamPulangJumatXi = KbmService::getJamPulangJumatXi();
        $durasiPelajaran = KbmService::getDurasiPelajaran('Senin');
        $durasiPelajaranJumat = KbmService::getDurasiPelajaran('Jumat');
        $toleransiTerlambat = KbmService::getToleransiTerlambat();
        $batasWaktuJurnal = KbmService::getBatasWaktuJurnal();
        $toleransiKelasKosong = KbmService::getToleransiKelasKosong();

        // Data Aturan Kepulangan Khusus
        $seninAdaUpacara = KbmService::isSeninAdaUpacara($todayDate);
        $jumatAdaPembiasaan = KbmService::isJumatAdaPembiasaan($todayDate);
        $acaraMendadak = KbmService::getAcaraMendadakInfo($todayDate);

        $acaraMendadakConfig = [
            'aktif' => Pengaturan::getVal('acara_mendadak_aktif', '0') == '1',
            'tanggal' => Pengaturan::getVal('acara_mendadak_tanggal', $todayDate),
            'jam_pulang' => Pengaturan::getVal('acara_mendadak_jam_pulang', '11:30'),
            'alasan' => Pengaturan::getVal('acara_mendadak_alasan', ''),
            'target' => Pengaturan::getVal('acara_mendadak_target', 'semua'),
        ];

        // Perbandingan Jam Pulang Normal vs Efektif
        $jamPulangSeninNormal = KbmService::getJamPulangNormal('Senin');
        $jamPulangSeninEfektif = KbmService::getJamPulang('Senin', null, $todayDate, false);

        $jamPulangJumatXNormal = KbmService::getJamPulangNormal('Jumat', 'X');
        $jamPulangJumatXEfektif = KbmService::getJamPulangJumatX($todayDate, false);

        $jamPulangJumatXiNormal = KbmService::getJamPulangNormal('Jumat', 'XI');
        $jamPulangJumatXiEfektif = KbmService::getJamPulangJumatXi($todayDate, false);

        // Hitung jam pulang jika tanpa upacara/pembiasaan (blade tidak bisa resolve namespace class langsung)
        $jamPulangSeninTanpaUpacara = KbmService::kurangiWaktuMenit($jamPulangSeninNormal, $durasiPelajaran);
        $jamPulangJumatXTanpaPembiasaan = KbmService::kurangiWaktuMenit($jamPulangJumatXNormal, $durasiPelajaranJumat);
        $jamPulangJumatXiTanpaPembiasaan = KbmService::kurangiWaktuMenit($jamPulangJumatXiNormal, $durasiPelajaranJumat);

        $seninKamisSlots = KbmService::getSlots('Senin');
        $seninKamisIstirahat = KbmService::getIstirahat('Senin');
        
        $jumatSlotsX = KbmService::getSlots('Jumat'); // Jam 1 s/d 13
        $jumatSlotsXi = array_filter($jumatSlotsX, function($k) {
            return (int)$k <= 12;
        }, ARRAY_FILTER_USE_KEY); // Jam 1 s/d 12

        $jumatIstirahat = KbmService::getIstirahat('Jumat');

        $isCustomSeninKamis = Pengaturan::getVal('kbm_slots_senin_kamis') !== null;
        $isCustomJumat = Pengaturan::getVal('kbm_slots_jumat') !== null;
        $isCustomIstirahatSeninKamis = Pengaturan::getVal('kbm_istirahat_senin_kamis') !== null;
        $isCustomIstirahatJumat = Pengaturan::getVal('kbm_istirahat_jumat') !== null;

        return view('admin.jam-sekolah.index', compact(
            'jamMasuk',
            'jamPulang',
            'jamPulangJumatX',
            'jamPulangJumatXi',
            'durasiPelajaran',
            'durasiPelajaranJumat',
            'toleransiTerlambat',
            'batasWaktuJurnal',
            'toleransiKelasKosong',
            'seninAdaUpacara',
            'jumatAdaPembiasaan',
            'acaraMendadak',
            'acaraMendadakConfig',
            'jamPulangSeninNormal',
            'jamPulangSeninEfektif',
            'jamPulangJumatXNormal',
            'jamPulangJumatXEfektif',
            'jamPulangJumatXiNormal',
            'jamPulangJumatXiEfektif',
            'jamPulangSeninTanpaUpacara',
            'jamPulangJumatXTanpaPembiasaan',
            'jamPulangJumatXiTanpaPembiasaan',
            'seninKamisSlots',
            'seninKamisIstirahat',
            'jumatSlotsX',
            'jumatSlotsXi',
            'jumatIstirahat',
            'isCustomSeninKamis',
            'isCustomJumat',
            'isCustomIstirahatSeninKamis',
            'isCustomIstirahatJumat'
        ));
    }

    /**
     * Simpan Pengaturan Jam Sekolah & Slot KBM (Admin)
     */
    public function updateJamSekolah(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required|string',
            'jam_pulang' => 'required|string',
            'jam_pulang_jumat_x' => 'required|string',
            'jam_pulang_jumat_xi' => 'required|string',
            'durasi_pelajaran_menit' => 'required|numeric|min:1',
            'durasi_pelajaran_jumat_menit' => 'required|numeric|min:1',
            'toleransi_keterlambatan_menit' => 'required|numeric|min:0',
            'batas_waktu_jurnal_menit' => 'required|numeric|min:0',
            'toleransi_kelas_kosong_menit' => 'required|numeric|min:0',
        ]);

        Pengaturan::setVal('jam_masuk', $request->jam_masuk, 'admin');
        Pengaturan::setVal('jam_pulang', $request->jam_pulang, 'admin');
        Pengaturan::setVal('jam_pulang_jumat_x', $request->jam_pulang_jumat_x, 'admin');
        Pengaturan::setVal('jam_pulang_jumat_xi', $request->jam_pulang_jumat_xi, 'admin');
        Pengaturan::setVal('jam_pulang_jumat', $request->jam_pulang_jumat_x, 'admin'); // Fallback sync
        Pengaturan::setVal('durasi_pelajaran_menit', $request->durasi_pelajaran_menit, 'admin');
        Pengaturan::setVal('durasi_pelajaran_jumat_menit', $request->durasi_pelajaran_jumat_menit, 'admin');
        Pengaturan::setVal('toleransi_keterlambatan_menit', $request->toleransi_keterlambatan_menit, 'admin');
        Pengaturan::setVal('batas_waktu_jurnal_menit', $request->batas_waktu_jurnal_menit, 'admin');
        Pengaturan::setVal('toleransi_kelas_kosong_menit', $request->toleransi_kelas_kosong_menit, 'piket');

        // Simpan custom slot KBM jika ada perubahan spesifik pada tabel slot
        if ($request->has('slots_senin_kamis') && is_array($request->slots_senin_kamis)) {
            $formattedSenin = [];
            foreach ($request->slots_senin_kamis as $jam => $data) {
                $formattedSenin[(int)$jam] = [
                    'waktu_mulai' => $data['mulai'] ?? '07:00',
                    'waktu_selesai' => $data['selesai'] ?? '07:40',
                    'keterangan' => !empty($data['keterangan']) ? $data['keterangan'] : null,
                ];
            }
            Pengaturan::setVal('kbm_slots_senin_kamis', json_encode($formattedSenin), 'admin');
        }

        if ($request->has('slots_jumat') && is_array($request->slots_jumat)) {
            $formattedJumat = [];
            foreach ($request->slots_jumat as $jam => $data) {
                $formattedJumat[(int)$jam] = [
                    'waktu_mulai' => $data['mulai'] ?? '07:00',
                    'waktu_selesai' => $data['selesai'] ?? '07:30',
                    'keterangan' => !empty($data['keterangan']) ? $data['keterangan'] : null,
                ];
            }
            Pengaturan::setVal('kbm_slots_jumat', json_encode($formattedJumat), 'admin');
        }

        // Simpan Jam Istirahat Senin-Kamis
        if ($request->has('istirahat_senin_kamis') && is_array($request->istirahat_senin_kamis)) {
            $formattedIstSenin = [];
            foreach ($request->istirahat_senin_kamis as $oldKey => $data) {
                $afterJam = (int)($data['setelah_jam'] ?? $oldKey);
                $mulai = $data['mulai'] ?? '09:40';
                $selesai = $data['selesai'] ?? '10:00';
                $formattedIstSenin[$afterJam] = [
                    'label' => $data['label'] ?? 'Istirahat',
                    'waktu' => $mulai . ' - ' . $selesai,
                    'waktu_mulai' => $mulai,
                    'waktu_selesai' => $selesai,
                ];
            }
            Pengaturan::setVal('kbm_istirahat_senin_kamis', json_encode($formattedIstSenin), 'admin');
        }

        // Simpan Jam Istirahat Jumat
        if ($request->has('istirahat_jumat') && is_array($request->istirahat_jumat)) {
            $formattedIstJumat = [];
            foreach ($request->istirahat_jumat as $oldKey => $data) {
                $afterJam = (int)($data['setelah_jam'] ?? $oldKey);
                $mulai = $data['mulai'] ?? '09:30';
                $selesai = $data['selesai'] ?? '09:50';
                $formattedIstJumat[$afterJam] = [
                    'label' => $data['label'] ?? 'Istirahat',
                    'waktu' => $mulai . ' - ' . $selesai,
                    'waktu_mulai' => $mulai,
                    'waktu_selesai' => $selesai,
                ];
            }
            Pengaturan::setVal('kbm_istirahat_jumat', json_encode($formattedIstJumat), 'admin');
        }

        // Simpan Aturan Kepulangan Senin & Jumat
        if ($request->has('senin_ada_upacara')) {
            Pengaturan::setVal('senin_ada_upacara', $request->input('senin_ada_upacara', '1'), 'admin');
        }
        if ($request->has('jumat_ada_pembiasaan')) {
            Pengaturan::setVal('jumat_ada_pembiasaan', $request->input('jumat_ada_pembiasaan', '1'), 'admin');
        }

        return redirect()->route('admin.jam-sekolah.index')
            ->with('success', 'Pengaturan Jam Sekolah berhasil disimpan dan langsung terhubung ke seluruh role.');
    }

    /**
     * Reset Pengaturan Jam Sekolah ke Standar
     */
    public function resetJamSekolah()
    {
        Pengaturan::setVal('jam_masuk', '07:00', 'admin');
        Pengaturan::setVal('jam_pulang', '15:00', 'admin');
        Pengaturan::setVal('jam_pulang_jumat_x', '15:30', 'admin');
        Pengaturan::setVal('jam_pulang_jumat_xi', '15:00', 'admin');
        Pengaturan::setVal('jam_pulang_jumat', '15:30', 'admin');
        Pengaturan::setVal('durasi_pelajaran_menit', 40, 'admin');
        Pengaturan::setVal('durasi_pelajaran_jumat_menit', 30, 'admin');
        Pengaturan::setVal('toleransi_keterlambatan_menit', 15, 'admin');
        Pengaturan::setVal('batas_waktu_jurnal_menit', 60, 'admin');
        Pengaturan::setVal('toleransi_kelas_kosong_menit', 15, 'piket');

        // Reset Aturan Kepulangan Khusus & Acara Mendadak
        Pengaturan::setVal('senin_ada_upacara', '1', 'admin');
        Pengaturan::setVal('jumat_ada_pembiasaan', '1', 'admin');
        Pengaturan::setVal('acara_mendadak_aktif', '0', 'admin');
        Pengaturan::where('kunci', 'senin_override_tanpa_upacara_tanggal')->delete();
        Pengaturan::where('kunci', 'jumat_override_tanpa_pembiasaan_tanggal')->delete();

        // Hapus custom slot agar fallback ke default SMKN 1 Boyolangu
        Pengaturan::where('kunci', 'kbm_slots_senin_kamis')->delete();
        Pengaturan::where('kunci', 'kbm_slots_jumat')->delete();
        Pengaturan::where('kunci', 'kbm_istirahat_senin_kamis')->delete();
        Pengaturan::where('kunci', 'kbm_istirahat_jumat')->delete();

        return redirect()->route('admin.jam-sekolah.index')
            ->with('success', 'Jadwal jam sekolah dan aturan kepulangan berhasil direset ke standar KBM.');
    }

    /**
     * Simpan / Perbarui Pengaturan Acara Mendadak (Pulang Cepat)
     */
    public function updateAcaraMendadak(Request $request)
    {
        $request->validate([
            'aktif' => 'required|in:0,1',
            'tanggal' => 'required_if:aktif,1|date',
            'jam_pulang' => 'required_if:aktif,1|string',
            'alasan' => 'required_if:aktif,1|string|max:255',
            'target' => 'required_if:aktif,1|in:semua,x,xi,xii',
        ]);

        $aktif = $request->input('aktif');
        Pengaturan::setVal('acara_mendadak_aktif', $aktif, 'admin');

        if ($aktif == '1') {
            $tanggal = $request->input('tanggal');
            $jamPulang = $request->input('jam_pulang');
            $alasan = $request->input('alasan');
            $target = $request->input('target', 'semua');

            Pengaturan::setVal('acara_mendadak_tanggal', $tanggal, 'admin');
            Pengaturan::setVal('acara_mendadak_jam_pulang', $jamPulang, 'admin');
            Pengaturan::setVal('acara_mendadak_alasan', $alasan, 'admin');
            Pengaturan::setVal('acara_mendadak_target', $target, 'admin');

            // Kirim notifikasi siaran jika opsi dicentang
            if ($request->boolean('kirim_notifikasi')) {
                $targetLabel = match($target) {
                    'x' => 'Khusus Kelas X (10)',
                    'xi' => 'Khusus Kelas XI (11)',
                    'xii' => 'Khusus Kelas XII (12)',
                    default => 'Seluruh Siswa (Kelas X, XI, & XII)'
                };

                $roles = ['guru', 'wali_kelas', 'siswa', 'ortu', 'piket', 'satpam', 'kepala'];
                $judul = '⚠️ Pengumuman Kepulangan Lebih Cepat';
                $pesan = "Pemberitahuan Resmi Sekolah: Pada tanggal {$tanggal}, siswa ({$targetLabel}) dipulangkan lebih awal pukul {$jamPulang} WIB sehubungan dengan: {$alasan}.";

                foreach ($roles as $r) {
                    Notifikasi::kirimKeRole($r, $judul, $pesan, route('notifikasi.index'), 'warning');
                }
            }

            return redirect()->route('admin.jam-sekolah.index')
                ->with('success', "Mode Acara Mendadak berhasil diaktifkan! Siswa dipulangkan pukul {$jamPulang} WIB.");
        } else {
            return redirect()->route('admin.jam-sekolah.index')
                ->with('success', 'Mode Acara Mendadak berhasil dinonaktifkan. Jadwal kepulangan kembali normal.');
        }
    }

    /**
     * Quick Toggle Upacara Senin
     */
    public function toggleUpacaraSenin(Request $request)
    {
        $status = $request->input('status', '1');
        Pengaturan::setVal('senin_ada_upacara', $status, 'admin');

        $pesan = ($status === '1')
            ? 'Status Upacara Senin: ADA UPACARA (Pulang normal pukul 15:00 WIB).'
            : 'Status Upacara Senin: TIDAK ADA UPACARA (Pulang maju 1 JP menjadi pukul 14:20 WIB).';

        return redirect()->route('admin.jam-sekolah.index')->with('success', $pesan);
    }

    /**
     * Quick Toggle Pembiasaan Jumat
     */
    public function togglePembiasaanJumat(Request $request)
    {
        $status = $request->input('status', '1');
        Pengaturan::setVal('jumat_ada_pembiasaan', $status, 'admin');

        $pesan = ($status === '1')
            ? 'Status Pembiasaan Jumat: ADA PEMBIASAAN (Pulang normal Kelas X: 15:30 WIB, Kelas XI/XII: 15:00 WIB).'
            : 'Status Pembiasaan Jumat: TIDAK ADA PEMBIASAAN (Pulang maju 1 JP -> Kelas X: 15:00 WIB, Kelas XI/XII: 14:30 WIB).';

        return redirect()->route('admin.jam-sekolah.index')->with('success', $pesan);
    }

    public function updateAdminSettings(Request $request)
    {
        $request->validate([
            'batas_waktu_jurnal_menit' => 'required|numeric|min:0',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'durasi_pelajaran_menit' => 'required|numeric|min:1',
        ]);

        Pengaturan::setVal('batas_waktu_jurnal_menit', $request->batas_waktu_jurnal_menit, 'admin');
        Pengaturan::setVal('jam_masuk', $request->jam_masuk, 'admin');
        Pengaturan::setVal('jam_pulang', $request->jam_pulang, 'admin');
        Pengaturan::setVal('durasi_pelajaran_menit', $request->durasi_pelajaran_menit, 'admin');

        return back()->with('success', 'Pengaturan sistem Admin berhasil disimpan.');
    }

    public function updateTeacherSettings(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        Pengaturan::setVal('notif_jurnal', $request->has('notif_jurnal') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('notif_presensi_masuk', $request->has('notif_presensi_masuk') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('notif_presensi_keluar', $request->has('notif_presensi_keluar') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('tema_tampilan', $request->input('tema_tampilan', 'light'), $role, $user->id_user);

        return back()->with('success', 'Preferensi Guru berhasil disimpan.');
    }

    public function updatePiketSettings(Request $request)
    {
        $request->validate([
            'toleransi_kelas_kosong_menit' => 'required|numeric|min:0',
        ]);

        Pengaturan::setVal('toleransi_kelas_kosong_menit', $request->toleransi_kelas_kosong_menit, 'piket');

        return back()->with('success', 'Preferensi Piket berhasil disimpan.');
    }

    public function updateSiswaSettings(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        Pengaturan::setVal('notif_jurnal', $request->has('notif_jurnal') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('notif_presensi_masuk', $request->has('notif_presensi_masuk') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('tema_tampilan', $request->input('tema_tampilan', 'light'), $role, $user->id_user);

        return back()->with('success', 'Preferensi Siswa berhasil disimpan.');
    }

    public function updateGeneralPreferences(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        Pengaturan::setVal('notif_jurnal', $request->has('notif_jurnal') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('notif_presensi_masuk', $request->has('notif_presensi_masuk') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('notif_presensi_keluar', $request->has('notif_presensi_keluar') ? 1 : 0, $role, $user->id_user);
        Pengaturan::setVal('tema_tampilan', $request->input('tema_tampilan', 'light'), $role, $user->id_user);

        return back()->with('success', 'Preferensi akun berhasil disimpan.');
    }
}
