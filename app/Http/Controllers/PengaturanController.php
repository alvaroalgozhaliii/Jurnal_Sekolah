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
        return redirect()->route('profil.show', ['tab' => 'pengaturan']);
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
