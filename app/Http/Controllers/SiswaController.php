<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Siswa::with(['kelas', 'user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $siswa = $query->orderBy('nama', 'asc')->get();
        return view('siswa.index', compact('siswa', 'search'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'nama' => 'required|string|max:150',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if ($request->filled('username')) {
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password ?? 'siswa123'),
                'role' => 'siswa',
                'aktif' => 1,
                'created_at' => now(),
            ]);
            $userId = $user->id_user;
        }

        Siswa::create([
            'id_user' => $userId,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => $request->has('aktif') ? 1 : 1,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas.jurusan', 'user', 'absensi.jurnal'])->findOrFail($id);
        return view('siswa.show', compact('siswa'));
    }

    public function edit($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $id . ',id_siswa',
            'nama' => 'required|string|max:150',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $siswa->update([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => $request->input('aktif', $siswa->aktif),
        ]);

        if ($siswa->user) {
            $siswa->user->update([
                'nama' => $request->nama,
            ]);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        Siswa::findOrFail($id)->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa dipindahkan ke trash');
    }

    public function trash()
    {
        $siswa = Siswa::onlyTrashed()->with('kelas')->get();
        return view('siswa.trash', compact('siswa'));
    }

    public function restore($id)
    {
        Siswa::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('siswa.trash')->with('success', 'Data siswa berhasil direstore');
    }

    public function forceDelete($id)
    {
        Siswa::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('siswa.trash')->with('success', 'Data siswa dihapus permanen');
    }
}