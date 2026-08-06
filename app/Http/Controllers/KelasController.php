<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();

        return view('kelas.index', compact('kelas'));
    }

    public function trash()
    {
        $kelas = Kelas::onlyTrashed()->get();

        return view('kelas.trash', compact('kelas'));
    }

    public function show($id)
    {
        $kelas = Kelas::findOrFail($id);

        return view('kelas.show', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'id_jurusan' => $request->id_jurusan,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);

        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'id_jurusan' => $request->id_jurusan,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diupdate');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus');
    }

    public function restore($id)
    {
        Kelas::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('kelas.trash')
            ->with('success', 'Data kelas berhasil direstore');
    }

    public function forceDelete($id)
    {
        Kelas::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('kelas.trash')
            ->with('success', 'Data kelas berhasil dihapus permanen');
    }
}