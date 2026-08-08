<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::with(['kelas', 'guru'])->get();
        return view('jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $guru = Guru::all();
        return view('jadwal.create', compact('kelas', 'guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|numeric',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'mapel' => 'required|string|max:100',
            'ruang' => 'nullable|string|max:20',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
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
            'aktif' => $request->has('aktif') ? 1 : 1,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show($id)
    {
        $jadwal = Jadwal::with(['kelas', 'guru'])->findOrFail($id);
        return view('jadwal.show', compact('jadwal'));
    }

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $kelas = Kelas::all();
        $guru = Guru::all();
        return view('jadwal.edit', compact('jadwal', 'kelas', 'guru'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|numeric',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'mapel' => 'required|string|max:100',
            'ruang' => 'nullable|string|max:20',
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
            'aktif' => $request->input('aktif', $jadwal->aktif),
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diubah');
    }

    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal dipindahkan ke trash');
    }

    public function trash()
    {
        $jadwal = Jadwal::onlyTrashed()->with(['kelas', 'guru'])->get();
        return view('jadwal.trash', compact('jadwal'));
    }

    public function restore($id)
    {
        Jadwal::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('jadwal.trash')->with('success', 'Jadwal berhasil direstore');
    }

    public function forceDelete($id)
    {
        Jadwal::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('jadwal.trash')->with('success', 'Jadwal dihapus permanen');
    }
}