<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\JurnalHarian;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AbsensiSiswa::with(['jurnal.jadwal.kelas', 'siswa', 'user']);

        if ($user->isSiswa() && $user->siswa) {
            $query->where('id_siswa', $user->siswa->id_siswa);
        } elseif ($user->isGuru() && $user->guru) {
            $guruId = $user->guru->id_guru;
            $query->whereHas('jurnal', function ($q) use ($guruId) {
                $q->where('id_guru', $guruId);
            });
        }

        $absensi = $query->orderBy('created_at', 'desc')->get();
        return view('absensi-siswa.index', compact('absensi'));
    }

    public function create(Request $request)
    {
        $idJurnal = $request->get('id_jurnal');
        $jurnalSelected = null;
        $siswaList = collect();

        if ($idJurnal) {
            $jurnalSelected = JurnalHarian::with(['jadwal.kelas.siswa'])->find($idJurnal);
            if ($jurnalSelected && $jurnalSelected->jadwal && $jurnalSelected->jadwal->kelas) {
                $siswaList = Siswa::where('id_kelas', $jurnalSelected->jadwal->id_kelas)->get();
            }
        }

        $jurnalList = JurnalHarian::with('jadwal.kelas')->orderBy('tanggal', 'desc')->get();
        return view('absensi-siswa.create', compact('jurnalList', 'jurnalSelected', 'siswaList'));
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'id_jurnal' => 'required',
            'absensi' => 'required|array', // [id_siswa => status]
        ]);

        $idJurnal = $request->id_jurnal;

        foreach ($request->absensi as $idSiswa => $status) {
            $keterangan = $request->keterangan[$idSiswa] ?? null;

            AbsensiSiswa::updateOrCreate(
                [
                    'id_jurnal' => $idJurnal,
                    'id_siswa' => $idSiswa,
                ],
                [
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'dicatat_oleh' => Auth::id(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->route('absensi-siswa.index')->with('success', 'Absensi siswa berhasil dicatat.');
    }

    public function show($id)
    {
        $absensi = AbsensiSiswa::with(['jurnal.jadwal.kelas', 'siswa', 'user'])->findOrFail($id);
        return view('absensi-siswa.show', compact('absensi'));
    }

    public function edit($id)
    {
        $absensi = AbsensiSiswa::with(['jurnal', 'siswa'])->findOrFail($id);
        return view('absensi-siswa.edit', compact('absensi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin,alpa,terlambat',
        ]);

        $absensi = AbsensiSiswa::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'dicatat_oleh' => Auth::id(),
        ]);

        return redirect()->route('absensi-siswa.index')->with('success', 'Data absensi siswa berhasil diubah.');
    }

    public function destroy($id)
    {
        $absensi = AbsensiSiswa::findOrFail($id);
        $absensi->delete();
        return redirect()->route('absensi-siswa.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}