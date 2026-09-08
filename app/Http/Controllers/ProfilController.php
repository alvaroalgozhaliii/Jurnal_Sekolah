<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\TahunPelajaran;

class ProfilController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Detail relasi jika guru atau siswa
        $detailGuru = null;
        $detailSiswa = null;
        if ($user->guru) {
            $detailGuru = $user->guru()->with('kelasWali')->first();
        }
        if ($user->siswa) {
            $detailSiswa = $user->siswa()->with('kelas')->first();
        }

        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();
        $activeTab = $request->query('tab', 'profil');

        return view('profil.show', compact(
            'user',
            'role',
            'detailGuru',
            'detailSiswa',
            'tahunAktif',
            'activeTab'
        ));
    }

    /**
     * Update foto profil (halaman Profil Akun).
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('uploads/profil', $filename, 'public');

            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->update(['foto_profil' => $path]);
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Hapus foto profil (halaman Profil Akun).
     */
    public function deleteFoto(Request $request)
    {
        $user = Auth::user();

        if ($user->foto_profil) {
            if (Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->update(['foto_profil' => null]);
            return back()->with('success', 'Foto profil berhasil dihapus.');
        }

        return back()->with('error', 'Anda tidak memiliki foto profil untuk dihapus.');
    }

    /**
     * Ubah username (dari halaman Pengaturan).
     */
    public function updateUsername(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id_user . ',id_user',
        ]);

        $user->update(['username' => $request->username]);

        return back()->with('success', 'Username berhasil diubah.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai.');
        }

        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
