<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\JurnalHarian;
use App\Models\Siswa;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $query = AbsensiSiswa::with(['jurnal.jadwal.kelas', 'siswa', 'user']);

        if ($user->isOrtu()) {
            $anakIds = $user->anakList->pluck('id_siswa');
            $query->whereIn('id_siswa', $anakIds);
        } elseif ($user->isGuru() && !$user->isAdmin() && $user->guru) {
            $guruId = $user->guru->id_guru;
            $query->whereHas('jurnal', function ($q) use ($guruId) {
                $q->where('id_guru', $guruId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function($qs) use ($search) {
                      $qs->where('nama', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jurnal.jadwal.kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $absensi = $query->orderBy('created_at', 'desc')->get();
        return view('absensi-siswa.index', compact('absensi', 'search'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $idJurnal = $request->get('id_jurnal');
        $jurnalSelected = null;
        $siswaList = collect();

        if ($idJurnal) {
            $jurnalSelected = JurnalHarian::with(['jadwal.kelas.siswa'])->find($idJurnal);
            if ($jurnalSelected && $jurnalSelected->jadwal && $jurnalSelected->jadwal->kelas) {
                $siswaList = Siswa::where('id_kelas', $jurnalSelected->jadwal->id_kelas)->get();
            }
        } else {
            // Auto pick latest active journal for teacher
            if ($user->isGuru() && $user->guru) {
                $jurnalSelected = JurnalHarian::with(['jadwal.kelas.siswa'])
                    ->where('id_guru', $user->guru->id_guru)
                    ->orderBy('tanggal', 'desc')
                    ->first();
                if ($jurnalSelected && $jurnalSelected->jadwal) {
                    $siswaList = Siswa::where('id_kelas', $jurnalSelected->jadwal->id_kelas)->get();
                }
            }
        }

        if ($user->isGuru() && $user->guru) {
            $jurnalList = JurnalHarian::with('jadwal.kelas')
                ->where('id_guru', $user->guru->id_guru)
                ->orderBy('tanggal', 'desc')
                ->get();
        } else {
            $jurnalList = JurnalHarian::with('jadwal.kelas')->orderBy('tanggal', 'desc')->get();
        }

        return view('absensi-siswa.create', compact('jurnalList', 'jurnalSelected', 'siswaList'));
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'id_jurnal' => 'required|exists:jurnal_harian,id_jurnal',
            'absensi' => 'required|array', // [id_siswa => status]
        ]);

        $idJurnal = $request->id_jurnal;
        $jurnal = JurnalHarian::findOrFail($idJurnal);

        foreach ($request->absensi as $idSiswa => $status) {
            $keterangan = $request->keterangan[$idSiswa] ?? null;
            $jamMasuk = $request->jam_masuk[$idSiswa] ?? null;
            $menitTerlambat = $request->menit_terlambat[$idSiswa] ?? null;

            if ($status === 'terlambat' && !$jamMasuk) {
                $jamMasuk = Carbon::now()->format('H:i');
            }

            $record = AbsensiSiswa::updateOrCreate(
                [
                    'id_jurnal' => $idJurnal,
                    'id_siswa' => $idSiswa,
                ],
                [
                    'status' => $status,
                    'jam_masuk' => $jamMasuk,
                    'menit_terlambat' => $menitTerlambat,
                    'keterangan' => $keterangan,
                    'dicatat_oleh' => Auth::id(),
                    'created_at' => now(),
                ]
            );

            // Send notification to Ortu if ALPA
            if ($status === 'alpa') {
                $siswa = Siswa::with('ortu')->find($idSiswa);
                if ($siswa) {
                    // Send to linked ortu accounts
                    foreach ($siswa->ortu as $ortuUser) {
                        Notifikasi::kirim(
                            $ortuUser->id_user,
                            'Pemberitahuan ALPA Siswa',
                            'Anak Anda, ' . $siswa->nama . ', tercatat ALPA pada tanggal ' . $jurnal->tanggal . ' (' . $jurnal->mapel . ').',
                            route('ortu.presensi'),
                            'alpa'
                        );
                    }
                    // Fallback to direct id_user if pivot empty
                    if ($siswa->ortu->isEmpty() && $siswa->id_user) {
                        Notifikasi::kirim(
                            $siswa->id_user,
                            'Pemberitahuan ALPA Siswa',
                            'Anak Anda, ' . $siswa->nama . ', tercatat ALPA pada tanggal ' . $jurnal->tanggal . ' (' . $jurnal->mapel . ').',
                            route('ortu.presensi'),
                            'alpa'
                        );
                    }
                }
            }
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
        if (str_contains($absensi->keterangan ?? '', 'Orang Tua')) {
            return redirect()->route('absensi-siswa.show', $id)->with('error', 'Data absensi dari izin orang tua bersifat resmi dan tidak dapat diedit.');
        }
        return view('absensi-siswa.edit', compact('absensi'));
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiSiswa::with('jurnal.jadwal')->findOrFail($id);
        if (str_contains($absensi->keterangan ?? '', 'Orang Tua')) {
            return redirect()->route('absensi-siswa.show', $id)->with('error', 'Data absensi dari izin orang tua bersifat resmi dan tidak dapat diubah.');
        }

        $request->validate([
            'status' => 'required|in:hadir,sakit,izin,alpa,terlambat',
            'jam_masuk' => 'nullable',
            'menit_terlambat' => 'nullable|integer',
        ]);

        $absensi->update([
            'status' => $request->status,
            'jam_masuk' => $request->jam_masuk,
            'menit_terlambat' => $request->menit_terlambat,
            'keterangan' => $request->keterangan,
            'dicatat_oleh' => Auth::id(),
        ]);

        if ($request->status === 'alpa') {
            $siswa = Siswa::with('ortu')->find($absensi->id_siswa);
            if ($siswa) {
                foreach ($siswa->ortu as $ortuUser) {
                    Notifikasi::kirim(
                        $ortuUser->id_user,
                        'Pemberitahuan ALPA Siswa',
                        'Anak Anda, ' . $siswa->nama . ', tercatat ALPA pada tanggal ' . ($absensi->jurnal?->tanggal ?? date('Y-m-d')) . '.',
                        route('ortu.presensi'),
                        'alpa'
                    );
                }
            }
        }

        return redirect()->route('absensi-siswa.index')->with('success', 'Data absensi siswa berhasil diubah.');
    }

    public function destroy($id)
    {
        $absensi = AbsensiSiswa::findOrFail($id);
        if (str_contains($absensi->keterangan ?? '', 'Orang Tua')) {
            return redirect()->route('absensi-siswa.index')->with('error', 'Data absensi dari izin orang tua bersifat resmi dan tidak dapat dihapus.');
        }
        $absensi->delete();
        return redirect()->route('absensi-siswa.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}