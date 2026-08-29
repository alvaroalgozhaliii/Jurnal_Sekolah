<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = MataPelajaran::query();

        if ($search) {
            $query->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
        }

        $mapel = $query->orderBy('nama_mapel', 'asc')->get();

        return view('mapel.index', compact('mapel', 'search'));
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mata_pelajaran,nama_mapel',
            'kode_mapel' => 'nullable|string|max:20',
        ]);

        MataPelajaran::create([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mata_pelajaran,nama_mapel,' . $id . ',id_mapel',
            'kode_mapel' => 'nullable|string|max:20',
        ]);

        $mapel->update([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil diubah');
    }

    public function show($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('mapel.show', compact('mapel'));
    }

    public function destroy($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil dipindahkan ke trash');
    }

    public function trash()
    {
        $mapel = MataPelajaran::onlyTrashed()->get();
        return view('mapel.trash', compact('mapel'));
    }

    public function restore($id)
    {
        MataPelajaran::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('mapel.trash')->with('success', 'Mata Pelajaran berhasil direstore');
    }

    public function forceDelete($id)
    {
        MataPelajaran::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('mapel.trash')->with('success', 'Mata Pelajaran berhasil dihapus permanen');
    }
}
