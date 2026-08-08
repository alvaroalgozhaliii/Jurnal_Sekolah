<?php

namespace App\Http\Controllers;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        $tahun = TahunPelajaran::orderBy('tahun', 'desc')->get();
        return view('tahun-pelajaran.index', compact('tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        if ($request->has('aktif')) {
            TahunPelajaran::query()->update(['aktif' => false]);
        }

        TahunPelajaran::create([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        return back()->with('success', 'Tahun Pelajaran berhasil ditambahkan.');
    }

    public function setAktif($id)
    {
        TahunPelajaran::query()->update(['aktif' => false]);
        $tp = TahunPelajaran::findOrFail($id);
        $tp->update(['aktif' => true]);

        return back()->with('success', "Tahun Pelajaran {$tp->tahun} ({$tp->semester}) diaktifkan.");
    }

    public function destroy($id)
    {
        $tp = TahunPelajaran::findOrFail($id);
        $tp->delete();
        return back()->with('success', 'Tahun Pelajaran berhasil dihapus.');
    }
}
