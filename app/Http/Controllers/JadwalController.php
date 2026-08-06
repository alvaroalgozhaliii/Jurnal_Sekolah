<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::all();

        return view('jadwal.index', compact('jadwal'));
    }

    public function trash()
    {
        $jadwal = Jadwal::onlyTrashed()->get();

        return view('jadwal.trash', compact('jadwal'));
    }

    public function create()
    {
        return view('jadwal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required',
            'jam_ke' => 'required',
            'id_kelas' => 'required',
            'id_guru' => 'required',
            'mapel' => 'required',
            'ruang' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
        ]);

        Jadwal::create([
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'id_kelas' => $request->id_kelas,
            'id_guru' => $request->id_guru,
            'mapel' => $request->mapel,
            'ruang' => $request->ruang,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'aktif' => $request->aktif ?? 1,
        ]);

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(Jadwal $jadwal)
    {
        return view('jadwal.show', compact('jadwal'));
    }

    public function edit(Jadwal $jadwal)
    {
        return view('jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'hari' => 'required',
            'jam_ke' => 'required',
            'id_kelas' => 'required',
            'id_guru' => 'required',
            'mapel' => 'required',
            'ruang' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
        ]);

        $jadwal->update([
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'id_kelas' => $request->id_kelas,
            'id_guru' => $request->id_guru,
            'mapel' => $request->mapel,
            'ruang' => $request->ruang,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'aktif' => $request->aktif ?? 1,
        ]);

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil diubah');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    public function restore($id)
    {
        Jadwal::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('jadwal.trash')
            ->with('success', 'Jadwal berhasil direstore');
    }

    public function forceDelete($id)
    {
        Jadwal::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('jadwal.trash')
            ->with('success', 'Jadwal berhasil dihapus permanen');
    }
}