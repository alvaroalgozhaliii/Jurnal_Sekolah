<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::all();
        return view('mapel.index', compact('mapel'));
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'nullable|string|max:20',
            'nama_mapel' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        Mapel::create([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Data Mata Pelajaran berhasil ditambahkan.');
    }

    public function show($id)
    {
        $mapel = Mapel::findOrFail($id);
        return view('mapel.show', compact('mapel'));
    }

    public function edit($id)
    {
        $mapel = Mapel::findOrFail($id);
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'kode_mapel' => 'nullable|string|max:20',
            'nama_mapel' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $mapel->update([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Data Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Mapel::findOrFail($id)->delete();
        return redirect()->route('mapel.index')->with('success', 'Data Mata Pelajaran dipindahkan ke Sampah.');
    }

    public function trash()
    {
        $mapel = Mapel::onlyTrashed()->get();
        return view('mapel.trash', compact('mapel'));
    }

    public function restore($id)
    {
        Mapel::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('mapel.trash')->with('success', 'Data Mata Pelajaran berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        Mapel::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('mapel.trash')->with('success', 'Data Mata Pelajaran dihapus permanen.');
    }
}
