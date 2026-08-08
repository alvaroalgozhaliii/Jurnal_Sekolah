<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profil.show', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id_user . ',id_user',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
        ]);

        // If user is linked to Guru
        if ($user->isGuru() && $user->guru) {
            $user->guru->update([
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
            ]);
        }

        // If user is linked to Siswa
        if ($user->isSiswa() && $user->siswa) {
            $user->siswa->update([
                'nama' => $request->nama,
            ]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
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
