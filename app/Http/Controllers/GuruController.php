<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::all();

        return view('guru.index', compact('guru'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        Guru::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
        ]);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan');
    }

    public function show($id)
    {
        $guru = Guru::findOrFail($id);

        return view('guru.show', compact('guru'));
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);

        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
        ]);

        return redirect()->route('guru.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        $guru->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Data berhasil dihapus');
    }

    public function restore($id)
    {
        Guru::withTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()->route('guru.index')
            ->with('success', 'Data berhasil direstore');
    }

    public function forceDelete($id)
    {
        Guru::withTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return redirect()->route('guru.index')
            ->with('success', 'Data berhasil dihapus permanen');
    }
}