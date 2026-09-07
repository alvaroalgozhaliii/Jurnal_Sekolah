<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\TahunPelajaran;

class WaliKelasAdminController extends Controller
{
    public function index(Request $request)
    {
        $tingkat = $request->get('tingkat');
        $idJurusan = $request->get('id_jurusan');
        $search = $request->get('search');
        $statusWali = $request->get('status_wali');

        $query = Kelas::with(['jurusan', 'guruWaliKelas.user', 'siswa']);

        if ($tingkat) {
            $query->where('tingkat', $tingkat);
        }

        if ($idJurusan) {
            $query->where('id_jurusan', $idJurusan);
        }

        if ($statusWali === 'assigned') {
            $query->whereNotNull('id_guru_walikelas');
        } elseif ($statusWali === 'unassigned') {
            $query->whereNull('id_guru_walikelas');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('wali_kelas', 'like', "%{$search}%")
                  ->orWhereHas('guruWaliKelas', function($qg) use ($search) {
                      $qg->where('nama', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
                  });
            });
        }

        $kelasList = $query->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        $guruList = Guru::orderBy('nama', 'asc')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        $totalKelas = Kelas::count();
        $totalAssigned = Kelas::whereNotNull('id_guru_walikelas')->count();
        $totalUnassigned = $totalKelas - $totalAssigned;

        return view('admin.wali-kelas.index', compact(
            'kelasList',
            'guruList',
            'jurusanList',
            'tahunAktif',
            'tingkat',
            'idJurusan',
            'search',
            'statusWali',
            'totalKelas',
            'totalAssigned',
            'totalUnassigned'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_guru' => 'nullable|exists:guru,id_guru',
        ]);

        $kelas = Kelas::findOrFail($id);

        if ($request->filled('id_guru')) {
            $guru = Guru::findOrFail($request->id_guru);

            // Cek jika guru sudah menjadi wali kelas di kelas lain
            $existing = Kelas::where('id_guru_walikelas', $guru->id_guru)
                ->where('id_kelas', '!=', $id)
                ->first();

            if ($existing) {
                // Pindahkan atau beri konfirmasi: kosongkan dari kelas lama
                $existing->update([
                    'id_guru_walikelas' => null,
                    'wali_kelas' => null,
                ]);
            }

            $kelas->update([
                'id_guru_walikelas' => $guru->id_guru,
                'wali_kelas' => $guru->nama,
            ]);

            return back()->with('success', "Wali kelas {$kelas->nama_kelas} berhasil diperbarui: {$guru->nama}.");
        } else {
            $kelas->update([
                'id_guru_walikelas' => null,
                'wali_kelas' => null,
            ]);

            return back()->with('success', "Wali kelas {$kelas->nama_kelas} berhasil dikosongkan.");
        }
    }

    public function exportCsv()
    {
        $kelasList = Kelas::with(['jurusan', 'guruWaliKelas'])->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data_wali_kelas_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($kelasList) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No', 'Tingkat', 'Nama Kelas', 'Jurusan', 'Nama Wali Kelas', 'NIP Wali Kelas', 'No Telepon', 'Jumlah Siswa']);

            $no = 1;
            foreach ($kelasList as $k) {
                fputcsv($file, [
                    $no++,
                    $k->tingkat,
                    $k->nama_kelas,
                    $k->jurusan->nama_jurusan ?? '-',
                    $k->guruWaliKelas->nama ?? ($k->wali_kelas ?? 'Belum Ditentukan'),
                    $k->guruWaliKelas->nip ?? '-',
                    $k->guruWaliKelas->no_telp ?? '-',
                    $k->siswa->count(),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
