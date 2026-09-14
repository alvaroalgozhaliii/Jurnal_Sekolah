<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Models\TahunPelajaran;

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
        $jamMasuk = KbmService::getJamMasuk();
        $jamPulang = KbmService::getJamPulang('Senin');
        $jamPulangJumat = KbmService::getJamPulang('Jumat');
        $durasiPelajaran = KbmService::getDurasiPelajaran('Senin');
        $durasiPelajaranJumat = KbmService::getDurasiPelajaran('Jumat');
        $toleransiTerlambat = KbmService::getToleransiTerlambat();
        $batasWaktuJurnal = KbmService::getBatasWaktuJurnal();
        $toleransiKelasKosong = KbmService::getToleransiKelasKosong();

        $seninKamisSlots = KbmService::getSlots('Senin');
        $seninKamisIstirahat = KbmService::getIstirahat('Senin');
        $jumatSlots = KbmService::getSlots('Jumat');
        $jumatIstirahat = KbmService::getIstirahat('Jumat');

        $isCustomSeninKamis = Pengaturan::getVal('kbm_slots_senin_kamis') !== null;
        $isCustomJumat = Pengaturan::getVal('kbm_slots_jumat') !== null;

        return view('admin.jam-sekolah.index', compact(
            'jamMasuk',
            'jamPulang',
            'jamPulangJumat',
            'durasiPelajaran',
            'durasiPelajaranJumat',
            'toleransiTerlambat',
            'batasWaktuJurnal',
            'toleransiKelasKosong',
            'seninKamisSlots',
            'seninKamisIstirahat',
            'jumatSlots',
            'jumatIstirahat',
            'isCustomSeninKamis',
            'isCustomJumat'
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
            'jam_pulang_jumat' => 'required|string',
            'durasi_pelajaran_menit' => 'required|numeric|min:1',
            'durasi_pelajaran_jumat_menit' => 'required|numeric|min:1',
            'toleransi_keterlambatan_menit' => 'required|numeric|min:0',
            'batas_waktu_jurnal_menit' => 'required|numeric|min:0',
            'toleransi_kelas_kosong_menit' => 'required|numeric|min:0',
        ]);

        Pengaturan::setVal('jam_masuk', $request->jam_masuk, 'admin');
        Pengaturan::setVal('jam_pulang', $request->jam_pulang, 'admin');
        Pengaturan::setVal('jam_pulang_jumat', $request->jam_pulang_jumat, 'admin');
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
        Pengaturan::setVal('jam_pulang_jumat', '15:30', 'admin');
        Pengaturan::setVal('durasi_pelajaran_menit', 40, 'admin');
        Pengaturan::setVal('durasi_pelajaran_jumat_menit', 30, 'admin');
        Pengaturan::setVal('toleransi_keterlambatan_menit', 15, 'admin');
        Pengaturan::setVal('batas_waktu_jurnal_menit', 60, 'admin');
        Pengaturan::setVal('toleransi_kelas_kosong_menit', 15, 'piket');

        // Hapus custom slot agar fallback ke default SMKN 1 Boyolangu
        Pengaturan::where('kunci', 'kbm_slots_senin_kamis')->delete();
        Pengaturan::where('kunci', 'kbm_slots_jumat')->delete();
        Pengaturan::where('kunci', 'kbm_istirahat_senin_kamis')->delete();
        Pengaturan::where('kunci', 'kbm_istirahat_jumat')->delete();

        return redirect()->route('admin.jam-sekolah.index')
            ->with('success', 'Jadwal jam sekolah berhasil direset ke standar KBM.');
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
