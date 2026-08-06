<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::all();

        return view('siswa.index', compact('siswa'));
    }

    public function trash()
    {
        $siswa = Siswa::onlyTrashed()->get();

        return view('siswa.trash', compact('siswa'));
    }

    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);

        return view('siswa.show', compact('siswa'));
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(Request $request)
    {
        Siswa::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => $request->aktif,
        ]);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);

        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $siswa->update([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => $request->aktif,
        ]);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        Siswa::findOrFail($id)->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus');
    }

    public function restore($id)
    {
        Siswa::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('siswa.trash')
            ->with('success', 'Data siswa berhasil direstore');
    }

    public function forceDelete($id)
    {
        Siswa::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('siswa.trash')
            ->with('success', 'Data siswa berhasil dihapus permanen');
    }
}