<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jurusan')->get();
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $jurusan = Jurusan::all();
        return view('kelas.create', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas',
            'tingkat' => 'required|in:X,XI,XII',
            'id_jurusan' => 'nullable|exists:jurusan,id_jurusan',
            'wali_kelas' => 'nullable|string|max:150',
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'id_jurusan' => $request->id_jurusan,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function show($id)
    {
        $kelas = Kelas::with(['jurusan', 'siswa', 'jadwal.guru'])->findOrFail($id);
        return view('kelas.show', compact('kelas'));
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jurusan = Jurusan::all();
        return view('kelas.edit', compact('kelas', 'jurusan'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas,' . $id . ',id_kelas',
            'tingkat' => 'required|in:X,XI,XII',
            'id_jurusan' => 'nullable|exists:jurusan,id_jurusan',
            'wali_kelas' => 'nullable|string|max:150',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'id_jurusan' => $request->id_jurusan,
            'wali_kelas' => $request->wali_kelas,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diupdate');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();
        return redirect()->route('kelas.index')->with('success', 'Data kelas dipindahkan ke trash');
    }

    public function trash()
    {
        $kelas = Kelas::onlyTrashed()->with('jurusan')->get();
        return view('kelas.trash', compact('kelas'));
    }

    public function restore($id)
    {
        Kelas::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('kelas.trash')->with('success', 'Data kelas berhasil direstore');
    }

    public function forceDelete($id)
    {
        Kelas::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('kelas.trash')->with('success', 'Data kelas dihapus permanen');
    }
}