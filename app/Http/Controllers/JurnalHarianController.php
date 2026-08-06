<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class JurnalHarianController extends Controller
{
    public function index()
    {
        $jurnal_harian = JurnalHarian::with(['guru', 'kelas'])->get();

        return view('jurnal_harian.index', compact('jurnal_harian'));
    }


    public function create()
    {
        $guru = Guru::all();
        $kelas = Kelas::all();

        return view('jurnal_harian.create', compact('guru', 'kelas'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'id_guru' => 'required',
            'id_kelas' => 'required',
            'mata_pelajaran' => 'required',
            'materi' => 'required',
        ]);


        JurnalHarian::create([
            'tanggal' => $request->tanggal,
            'id_guru' => $request->id_guru,
            'id_kelas' => $request->id_kelas,
            'mata_pelajaran' => $request->mata_pelajaran,
            'materi' => $request->materi,
            'keterangan' => $request->keterangan,
        ]);


        return redirect()
            ->route('jurnal-harian.index')
            ->with('success', 'Jurnal berhasil ditambahkan');
    }


    public function show($id)
    {
        $jurnal_harian = JurnalHarian::with(['guru', 'kelas'])
            ->findOrFail($id);


        return view('jurnal_harian.show', compact('jurnal_harian'));
    }


    public function edit($id)
    {
        $jurnal_harian = JurnalHarian::findOrFail($id);

        $guru = Guru::all();
        $kelas = Kelas::all();


        return view('jurnal_harian.edit', compact(
            'jurnal_harian',
            'guru',
            'kelas'
        ));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'id_guru' => 'required',
            'id_kelas' => 'required',
            'mata_pelajaran' => 'required',
            'materi' => 'required',
        ]);


        $jurnal_harian = JurnalHarian::findOrFail($id);


        $jurnal_harian->update([
            'tanggal' => $request->tanggal,
            'id_guru' => $request->id_guru,
            'id_kelas' => $request->id_kelas,
            'mata_pelajaran' => $request->mata_pelajaran,
            'materi' => $request->materi,
            'keterangan' => $request->keterangan,
        ]);


        return redirect()
            ->route('jurnal-harian.index')
            ->with('success', 'Jurnal berhasil diperbarui');
    }


    public function destroy($id)
    {
        $jurnal_harian = JurnalHarian::findOrFail($id);

        $jurnal_harian->delete();


        return redirect()
            ->route('jurnal-harian.index')
            ->with('success', 'Jurnal masuk trash');
    }


    // =====================
    // TRASH
    // =====================

    public function trash()
    {
        $jurnal_harian = JurnalHarian::onlyTrashed()->get();


        return view('jurnal_harian.trash', compact('jurnal_harian'));
    }


    public function restore($id)
    {
        $jurnal_harian = JurnalHarian::onlyTrashed()
            ->findOrFail($id);


        $jurnal_harian->restore();


        return redirect()
            ->route('jurnal-harian.trash')
            ->with('success', 'Jurnal berhasil dikembalikan');
    }


    public function forceDelete($id)
    {
        $jurnal_harian = JurnalHarian::onlyTrashed()
            ->findOrFail($id);


        $jurnal_harian->forceDelete();


        return redirect()
            ->route('jurnal-harian.trash')
            ->with('success', 'Jurnal dihapus permanen');
    }
}