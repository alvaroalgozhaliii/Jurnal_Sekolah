<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Models\TahunPelajaran;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $tahunPelajaran = TahunPelajaran::all();
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        // Retrieve role settings
        $batasWaktuJurnal = Pengaturan::getVal('batas_waktu_jurnal_menit', 15, 'admin');
        $jamMasuk = Pengaturan::getVal('jam_masuk', '07:00', 'admin');
        $jamPulang = Pengaturan::getVal('jam_pulang', '15:30', 'admin');
        $durasiPelajaran = Pengaturan::getVal('durasi_pelajaran_menit', 45, 'admin');

        $notifJurnal = Pengaturan::getVal('notif_jurnal', 1, $role, $user->id_user);
        $notifPresensiMasuk = Pengaturan::getVal('notif_presensi_masuk', 1, $role, $user->id_user);
        $notifPresensiKeluar = Pengaturan::getVal('notif_presensi_keluar', 1, $role, $user->id_user);
        $temaTampilan = Pengaturan::getVal('tema_tampilan', 'light', $role, $user->id_user);

        $toleransiPiket = Pengaturan::getVal('toleransi_kelas_kosong_menit', 10, 'piket');

        return view('pengaturan.index', compact(
            'role',
            'user',
            'tahunPelajaran',
            'tahunAktif',
            'batasWaktuJurnal',
            'jamMasuk',
            'jamPulang',
            'durasiPelajaran',
            'notifJurnal',
            'notifPresensiMasuk',
            'notifPresensiKeluar',
            'temaTampilan',
            'toleransiPiket'
        ));
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
}
