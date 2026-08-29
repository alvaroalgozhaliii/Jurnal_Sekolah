<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('nama', 'asc')->get();
        return view('pengguna.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('pengguna.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,piket,ortu,siswa,walikelas,wali_kelas,waka_kesiswaan,waka_sdm,waka_kurikulum,kepala_sekolah,satpam',
            'no_hp' => 'nullable|string|max:25',
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'aktif' => 1,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pengguna = User::with(['guru', 'siswa.kelas'])->findOrFail($id);
        return view('pengguna.show', compact('pengguna'));
    }

    public function edit($id)
    {
        $pengguna = User::findOrFail($id);
        return view('pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, $id)
    {
        $pengguna = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id_user',
            'role' => 'required|in:admin,guru,piket,ortu,siswa,walikelas,wali_kelas,waka_kesiswaan,waka_sdm,waka_kurikulum,kepala_sekolah,satpam',
            'no_hp' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengguna = User::findOrFail($id);
        if ($pengguna->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $pengguna->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
