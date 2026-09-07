<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Siswa::with(['kelas', 'user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $siswa = $query->orderBy('nama', 'asc')->get();
        return view('siswa.index', compact('siswa', 'search'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $nisn = $request->input('nisn', $request->input('nis'));
        $request->merge(['nisn' => $nisn]);

        $request->validate([
            'nisn' => 'required|string|max:30|unique:siswa,nisn',
            'nama' => 'required|string|max:150',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if ($request->filled('username')) {
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password ?? 'siswa123'),
                'role' => 'siswa',
                'aktif' => 1,
                'created_at' => now(),
            ]);
            $userId = $user->id_user;
        }

        Siswa::create([
            'id_user' => $userId,
            'nisn' => $nisn,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => 1,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas.jurusan', 'user', 'absensi.jurnal'])->findOrFail($id);
        return view('siswa.show', compact('siswa'));
    }

    public function edit($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $nisn = $request->input('nisn', $request->input('nis'));
        $request->merge(['nisn' => $nisn]);

        $request->validate([
            'nisn' => 'required|string|max:30|unique:siswa,nisn,' . $id . ',id_siswa',
            'nama' => 'required|string|max:150',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $siswa->update([
            'nisn' => $nisn,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telp_ortu' => $request->no_telp_ortu,
            'aktif' => $request->input('aktif', $siswa->aktif),
        ]);

        if ($siswa->user) {
            $siswa->user->update([
                'nama' => $request->nama,
            ]);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        Siswa::findOrFail($id)->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa dipindahkan ke trash');
    }

    public function trash()
    {
        $siswa = Siswa::onlyTrashed()->with('kelas')->get();
        return view('siswa.trash', compact('siswa'));
    }

    public function restore($id)
    {
        Siswa::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('siswa.trash')->with('success', 'Data siswa berhasil direstore');
    }

    public function forceDelete($id)
    {
        Siswa::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('siswa.trash')->with('success', 'Data siswa dihapus permanen');
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_siswa.csv"',
        ];

        $sample = [
            ['nisn', 'nama', 'nama_kelas', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'no_telp_ortu'],
            ['0012345678', 'ACHMAD DANI', 'X RPL 1', 'L', 'Tulungagung', '2008-05-12', '081234567801'],
            ['0012345679', 'BELLA SAFITRI', 'X RPL 1', 'P', 'Tulungagung', '2008-08-20', '081234567802'],
        ];

        return response()->stream(function() use ($sample) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($sample as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:10240',
        ]);

        try {
            $parsed = \App\Services\CsvImportService::parseCsv($request->file('csv_file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses file CSV: ' . $e->getMessage());
        }

        @set_time_limit(0);
        try {
            DB::statement("SET SESSION innodb_lock_wait_timeout = 120");
        } catch (\Throwable $e) {}

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $rowNum = 1;

        // Pre-compute Bcrypt hash satu kali
        $defaultPasswordHash = Hash::make('siswa123');

        // Cache kelas, user, siswa di memori
        $cachedKelas = Kelas::all();
        $existingUsernames = User::pluck('id_user', 'username')->toArray();
        $existingSiswa = Siswa::pluck('id_siswa', 'nisn')->toArray();

        $findKelasCached = function($namaKls, $tingkatKls, $waliKls) use (&$cachedKelas) {
            if (empty($namaKls)) return null;
            $cleanName = str_replace(' ', '', strtolower($namaKls));
            foreach ($cachedKelas as $k) {
                if (strtolower($k->nama_kelas) === strtolower($namaKls) || str_replace(' ', '', strtolower($k->nama_kelas)) === $cleanName) {
                    if (!empty($waliKls) && empty($k->wali_kelas)) {
                        $k->update(['wali_kelas' => $waliKls]);
                    }
                    return $k;
                }
            }
            $t = $tingkatKls ?: (str_starts_with($namaKls, 'XII') ? 'XII' : (str_starts_with($namaKls, 'XI') ? 'XI' : 'X'));
            $newK = Kelas::create([
                'nama_kelas' => $namaKls,
                'tingkat' => $t,
                'wali_kelas' => $waliKls ?: null,
            ]);
            $cachedKelas->push($newK);
            return $newK;
        };

        DB::beginTransaction();
        try {
            foreach ($parsed['rows'] as $data) {
                $rowNum++;

                if ($rowNum % 50 === 0) {
                    DB::commit();
                    DB::beginTransaction();
                }

                $nama = trim($data['nama'] ?? ($data['nama_siswa'] ?? ''), " \t\n\r\0\x0B'\"");
                $namaKelas = trim($data['nama_kelas'] ?? ($data['kelas'] ?? ''));
                $namaWali = trim($data['nama_wali_kelas'] ?? ($data['wali_kelas'] ?? ''));
                $nisn = trim($data['nisn'] ?? ($data['nis'] ?? ($data['niss'] ?? '')));

                if (empty($nama)) {
                    $skipped++;
                    continue;
                }

                // Fallback NISN jika kosong
                $effectiveNisn = $nisn;
                if (empty($effectiveNisn)) {
                    $effectiveNisn = 'S' . strtoupper(substr(md5($nama . $namaKelas), 0, 9));
                }

                // Resolusi Kelas via Cache
                $idKelas = null;
                if (!empty($namaKelas)) {
                    $kelas = $findKelasCached($namaKelas, $data['tingkat'] ?? null, $namaWali);
                    if ($kelas) {
                        $idKelas = $kelas->id_kelas;
                    }
                }

                // Normalisasi Jenis Kelamin (L/P)
                $jkRaw = trim($data['l_p'] ?? ($data['lp'] ?? ($data['jenis_kelamin'] ?? ($data['jk'] ?? ''))));
                $jenisKelamin = in_array(strtoupper($jkRaw), ['L', 'LAKI-LAKI', 'LAKI', 'PRIA']) ? 'L' : (in_array(strtoupper($jkRaw), ['P', 'PEREMPUAN', 'WANITA']) ? 'P' : null);

                $idSiswa = $existingSiswa[$effectiveNisn] ?? null;
                $siswa = $idSiswa ? Siswa::find($idSiswa) : null;
                if (!$siswa && $idKelas) {
                    $siswa = Siswa::where('nama', $nama)->where('id_kelas', $idKelas)->first();
                }

                if (!$siswa) {
                    $username = 'siswa.' . preg_replace('/[^a-zA-Z0-9]/', '', $effectiveNisn);
                    $username = substr($username, 0, 26);
                    if (isset($existingUsernames[$username])) {
                        $username .= rand(100, 999);
                    }

                    $user = User::create([
                        'nama' => $nama,
                        'username' => $username,
                        'password' => $defaultPasswordHash,
                        'role' => 'ortu',
                        'aktif' => 1,
                    ]);
                    $existingUsernames[$username] = $user->id_user;

                    $newSiswa = Siswa::create([
                        'id_user' => $user->id_user,
                        'nisn' => $effectiveNisn,
                        'nama' => $nama,
                        'id_kelas' => $idKelas,
                        'jenis_kelamin' => $jenisKelamin,
                        'tempat_lahir' => $data['tempat_lahir'] ?? null,
                        'tanggal_lahir' => !empty($data['tanggal_lahir']) ? $data['tanggal_lahir'] : null,
                        'no_telp_ortu' => $data['no_telp_ortu'] ?? null,
                        'aktif' => 1,
                    ]);
                    $existingSiswa[$effectiveNisn] = $newSiswa->id_siswa;
                    $inserted++;
                } else {
                    $updateData = [];
                    if ($idKelas && $siswa->id_kelas != $idKelas) {
                        $updateData['id_kelas'] = $idKelas;
                    }
                    if ($jenisKelamin && empty($siswa->jenis_kelamin)) {
                        $updateData['jenis_kelamin'] = $jenisKelamin;
                    }
                    if (!empty($effectiveNisn) && empty($siswa->nisn)) {
                        $updateData['nisn'] = $effectiveNisn;
                    }
                    if (!empty($updateData)) {
                        $siswa->update($updateData);
                        $updated++;
                    }
                    $existingSiswa[$effectiveNisn] = $siswa->id_siswa;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', "Terjadi kesalahan pada baris {$rowNum}: " . $e->getMessage());
        }

        return back()->with('success', "Import CSV berhasil! {$inserted} data baru ditambahkan, {$updated} data diperbarui, {$skipped} dilewati.");
    }

    public function exportCsv(Request $request)
    {
        $search  = $request->get('search');
        $idKelas = $request->get('id_kelas');
        $query   = Siswa::with(['kelas', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('kelas', fn($qk) => $qk->where('nama_kelas', 'like', "%{$search}%"));
            });
        }

        if ($idKelas) {
            $query->where('id_kelas', $idKelas);
        }

        $data     = $query->orderBy('nama', 'asc')->get();
        $filename = 'data_siswa_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'No Telp Ortu', 'Username', 'Status']);
            foreach ($data as $i => $s) {
                fputcsv($out, [
                    $i + 1,
                    $s->NISN ?? '-',
                    $s->nama,
                    $s->kelas->nama_kelas ?? '-',
                    $s->jenis_kelamin === 'L' ? 'Laki-laki' : ($s->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                    $s->tempat_lahir ?? '-',
                    $s->tanggal_lahir ?? '-',
                    $s->no_telp_ortu ?? '-',
                    $s->user->username ?? '-',
                    $s->aktif ? 'Aktif' : 'Nonaktif',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}