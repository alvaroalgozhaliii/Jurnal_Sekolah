<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\JurnalHarian;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsensiSiswaController extends Controller
{
    public function index()
    {
        $absensi = AbsensiSiswa::all();

        return view('absensi-siswa.index', compact('absensi'));
    }

    public function create()
    {
        $jurnal = JurnalHarian::all();
        $siswa = Siswa::all();

        return view('absensi-siswa.create', compact('jurnal', 'siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jurnal' => 'required',
            'id_siswa' => 'required',
            'status' => 'required',
        ]);

        AbsensiSiswa::create([
            'id_jurnal' => $request->id_jurnal,
            'id_siswa' => $request->id_siswa,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'dicatat_oleh' => session('guru_id'),
            'created_at' => now(),
        ]);

        return redirect()->route('absensi-siswa.index')
            ->with('success', 'Absensi berhasil ditambahkan');
    }

    public function show($id)
    {
        $absensi = AbsensiSiswa::findOrFail($id);

        return view('absensi-siswa.show', compact('absensi'));
    }

    public function edit($id)
    {
        $absensi = AbsensiSiswa::findOrFail($id);

        $jurnal = JurnalHarian::all();

        $siswa = Siswa::all();

        return view('absensi-siswa.edit', compact(
            'absensi',
            'jurnal',
            'siswa'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_jurnal' => 'required',
            'id_siswa' => 'required',
            'status' => 'required',
        ]);

        $absensi = AbsensiSiswa::findOrFail($id);

        $absensi->update([
            'id_jurnal' => $request->id_jurnal,
            'id_siswa' => $request->id_siswa,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'dicatat_oleh' => session('guru_id'),
        ]);

        return redirect()->route('absensi-siswa.index')
            ->with('success', 'Absensi berhasil diupdate');
    }

    public function destroy($id)
{
    $absensi = AbsensiSiswa::findOrFail($id);

    $absensi->delete();

    return redirect()
        ->route('absensi-siswa.index')
        ->with('success', 'Data berhasil dihapus');
}
    

}   