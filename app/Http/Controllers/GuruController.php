<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Guru::with('user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('bidang_studi', 'like', "%{$search}%");
            });
        }

        $guru = $query->orderBy('nama', 'asc')->get();
        return view('guru.index', compact('guru', 'search'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:30|unique:guru,nip',
            'bidang_studi' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if ($request->filled('username')) {
            $user = User::create([
                'nama' => $request->nama,
                'nip' => $request->nip,
                'username' => $request->username,
                'password' => Hash::make($request->password ?? 'guru123'),
                'role' => 'guru',
                'aktif' => 1,
                'created_at' => now(),
            ]);
            $userId = $user->id_user;
        }

        Guru::create([
            'id_user' => $userId,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
            'created_at' => now(),
        ]);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan');
    }

    public function show($id)
    {
        $guru = Guru::with(['user', 'jadwal.kelas', 'jurnalHarian'])->findOrFail($id);
        return view('guru.show', compact('guru'));
    }

    public function edit($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:30|unique:guru,nip,' . $id . ',id_guru',
            'bidang_studi' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
        ]);

        if ($guru->user) {
            $guru->user->update([
                'nama' => $request->nama,
                'nip' => $request->nip,
            ]);
        }

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diupdate');
    }

    public function destroy($id)
    {
        Guru::findOrFail($id)->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru dipindahkan ke trash');
    }

    public function trash()
    {
        $guru = Guru::onlyTrashed()->get();
        return view('guru.trash', compact('guru'));
    }

    public function restore($id)
    {
        Guru::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('guru.trash')->with('success', 'Data guru berhasil direstore');
    }

    public function forceDelete($id)
    {
        Guru::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('guru.trash')->with('success', 'Data guru dihapus permanen');
    }
}