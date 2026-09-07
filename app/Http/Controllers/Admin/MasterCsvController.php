<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;

class MasterCsvController extends Controller
{
    public function index()
    {
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalWaliKelas = Kelas::whereNotNull('id_guru_walikelas')->count();

        $skFiles = [
            [
                'title' => 'SK Wali Kelas 2026/2027 (72 Rombel)',
                'filename' => 'sk_wali_kelas_2026_2027.csv',
                'desc' => 'Daftar 72 wali kelas untuk rombel X, XI, XII SMKN 1 Boyolangu.',
                'size' => file_exists(public_path('csv/sk_wali_kelas_2026_2027.csv')) ? round(filesize(public_path('csv/sk_wali_kelas_2026_2027.csv')) / 1024, 1) . ' KB' : '0 KB',
            ],
            [
                'title' => 'SK Pembagian Tugas Mengajar 2026/2027 (128 Guru)',
                'filename' => 'sk_pembagian_tugas_mengajar_2026_2027.csv',
                'desc' => 'Daftar pembagian tugas 128 guru, NIP, mapel, kelas, dan jumlah jam mengajar.',
                'size' => file_exists(public_path('csv/sk_pembagian_tugas_mengajar_2026_2027.csv')) ? round(filesize(public_path('csv/sk_pembagian_tugas_mengajar_2026_2027.csv')) / 1024, 1) . ' KB' : '0 KB',
            ],
            [
                'title' => 'SK Tugas Tambahan Guru 2026/2027 (33 Personil)',
                'filename' => 'sk_tugas_tambahan_2026_2027.csv',
                'desc' => 'Daftar Kepala Sekolah, Waka, Staf, Kepala Konsentrasi Keahlian, dan Bendahara.',
                'size' => file_exists(public_path('csv/sk_tugas_tambahan_2026_2027.csv')) ? round(filesize(public_path('csv/sk_tugas_tambahan_2026_2027.csv')) / 1024, 1) . ' KB' : '0 KB',
            ],
            [
                'title' => 'Template Master Import Multi-Role Terpadu',
                'filename' => 'template_master_import_sekolah.csv',
                'desc' => 'Format CSV serbaguna untuk memasukkan data Guru, Siswa, dan Wali Kelas sekaligus.',
                'size' => file_exists(public_path('csv/template_master_import_sekolah.csv')) ? round(filesize(public_path('csv/template_master_import_sekolah.csv')) / 1024, 1) . ' KB' : '0 KB',
            ],
        ];

        return view('admin.csv-master.index', compact(
            'totalGuru', 'totalSiswa', 'totalKelas', 'totalWaliKelas', 'skFiles'
        ));
    }

    public function importMaster(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:10240',
            'import_type' => 'nullable|in:auto,master,walikelas',
        ]);

        try {
            $parsed = \App\Services\CsvImportService::parseCsv($request->file('csv_file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses file CSV: ' . $e->getMessage());
        }

        // Set session timeout & execution time untuk dataset besar
        try {
            DB::statement("SET SESSION innodb_lock_wait_timeout = 120");
        } catch (\Throwable $e) {}
        @set_time_limit(0);

        $inserted = 0;
        $updated = 0;
        $rowNum = 1;

        // Pre-compute Bcrypt hash (karena Bcrypt sengaja lambat, jangan panggil di setiap iterasi loop)
        $defaultPassword = 'password123';
        $defaultPasswordHash = Hash::make($defaultPassword);

        // Cache kelas, username, dan NISN di memori untuk pencocokan cepat tanpa lock berulang
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
            // Buat baru jika belum ada
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

                // Commit berkala setiap 50 baris agar lock database segera dilepas
                if ($rowNum % 50 === 0) {
                    DB::commit();
                    DB::beginTransaction();
                }

                // Determine row role/type
                $role = strtolower(trim($data['role'] ?? ($data['kategori'] ?? '')));
                $nama = trim($data['nama'] ?? ($data['nama_guru'] ?? ($data['namaguru'] ?? ($data['nama_siswa'] ?? ($data['namasiswa'] ?? '')))), " \t\n\r\0\x0B'\"");
                $nisn = trim($data['nisn'] ?? ($data['nis'] ?? ($data['niss'] ?? '')));
                $nip = trim($data['nip'] ?? '');
                $nipNisn = trim($data['nip_nisn'] ?? ($data['nipnisn'] ?? ($nip ?: $nisn)));
                $namaKelas = trim($data['nama_kelas'] ?? ($data['namakelas'] ?? ($data['kelas'] ?? '')));
                $namaWali = trim($data['nama_wali_kelas'] ?? ($data['namawalikelas'] ?? ($data['wali_kelas'] ?? '')));
                $mapel = trim($data['mata_pelajaran'] ?? ($data['matapelajaran'] ?? ($data['mapel'] ?? ($data['bidang_studi'] ?? ($data['bidangstudi'] ?? '')))));
                $noHp = trim($data['no_hp'] ?? ($data['nohp'] ?? ($data['no_telp'] ?? ($data['notelp'] ?? ''))));
                $username = !empty($data['username']) ? trim($data['username']) : null;
                $password = !empty($data['password']) ? trim($data['password']) : 'password123';
                $jkRaw = trim($data['l_p'] ?? ($data['lp'] ?? ($data['jenis_kelamin'] ?? ($data['jk'] ?? ''))));
                $jenisKelamin = in_array(strtoupper($jkRaw), ['L', 'LAKI-LAKI', 'LAKI', 'PRIA']) ? 'L' : (in_array(strtoupper($jkRaw), ['P', 'PEREMPUAN', 'WANITA']) ? 'P' : null);

                if (empty($nama)) {
                    continue;
                }

                // If role not explicitly given, try detecting from context
                if (empty($role)) {
                    // Jika ada indikator siswa (NISN, jenis kelamin L/P, atau ada nomor siswa)
                    if (!empty($nisn) || !empty($data['niss']) || !empty($jkRaw) || isset($data['no'])) {
                        $role = 'siswa';
                    } elseif (!empty($data['nip']) || !empty($mapel)) {
                        $role = 'guru';
                    } elseif (!empty($namaWali) && $nama === $namaWali) {
                        $role = 'walikelas';
                    } elseif (!empty($data['tingkat']) && empty($nip)) {
                        $role = 'siswa';
                    } else {
                        $role = 'guru';
                    }
                }

                if ($role === 'walikelas') {
                    // 1. Wali Kelas Logic
                    $waliTarget = !empty($namaWali) ? $namaWali : $nama;
                    $tingkat = $data['tingkat'] ?? (str_starts_with($namaKelas, 'XII') ? 'XII' : (str_starts_with($namaKelas, 'XI') ? 'XI' : 'X'));

                    // Find or create Kelas via cache
                    $kelas = $findKelasCached($namaKelas, $tingkat, $waliTarget);

                    // Find or create Guru
                    $guru = null;
                    if (!empty($nipNisn)) {
                        $guru = Guru::where('nip', $nipNisn)->first();
                    }
                    if (!$guru) {
                        $guru = Guru::where('nama', $namaWali)->first();
                    }

                    if (!$guru) {
                        // Create User for this Guru
                        $userUname = $username ?: 'guru_' . Str::slug($namaWali, '');
                        $userUname = substr($userUname, 0, 26);
                        if (isset($existingUsernames[$userUname])) {
                            $userUname .= rand(100, 999);
                        }

                        $hashedPassword = ($password === $defaultPassword || empty($password)) ? $defaultPasswordHash : Hash::make($password);

                        $user = User::create([
                            'nama' => $namaWali,
                            'username' => $userUname,
                            'password' => $hashedPassword,
                            'role' => 'guru',
                            'no_hp' => $noHp ?: null,
                            'aktif' => 1,
                        ]);
                        $existingUsernames[$userUname] = $user->id_user;

                        $guru = Guru::create([
                            'id_user' => $user->id_user,
                            'nama' => $namaWali,
                            'nip' => $nipNisn ?: null,
                            'bidang_studi' => $mapel ?: 'Guru Mata Pelajaran',
                            'no_telp' => $noHp ?: null,
                            'created_at' => now(),
                        ]);
                        $inserted++;
                    } else {
                        $updated++;
                    }

                    // Assign Wali Kelas to Kelas
                    if ($kelas && $guru) {
                        $kelas->update([
                            'id_guru_walikelas' => $guru->id_guru,
                            'wali_kelas' => $guru->nama,
                        ]);
                    }

                } elseif ($role === 'guru') {
                    // 2. Guru Logic
                    $guru = null;
                    if (!empty($nipNisn)) {
                        $guru = Guru::where('nip', $nipNisn)->first();
                    }
                    if (!$guru) {
                        $guru = Guru::where('nama', $nama)->first();
                    }

                    if (!$guru) {
                        $userUname = $username ?: 'guru_' . Str::slug($nama, '');
                        $userUname = substr($userUname, 0, 30);
                        if (User::where('username', $userUname)->exists()) {
                            $userUname .= rand(10, 99);
                        }

                        $user = User::create([
                            'nama' => $nama,
                            'username' => $userUname,
                            'password' => Hash::make($password),
                            'role' => 'guru',
                            'no_hp' => $noHp ?: null,
                            'aktif' => 1,
                        ]);

                        Guru::create([
                            'id_user' => $user->id_user,
                            'nama' => $nama,
                            'nip' => $nipNisn ?: null,
                            'bidang_studi' => $mapel ?: 'Guru Mata Pelajaran',
                            'no_telp' => $noHp ?: null,
                            'created_at' => now(),
                        ]);
                        $inserted++;
                    } else {
                        $guru->update([
                            'bidang_studi' => $mapel ?: $guru->bidang_studi,
                            'no_telp' => $noHp ?: $guru->no_telp,
                        ]);
                        $updated++;
                    }

                } elseif ($role === 'siswa') {
                    // 3. Siswa Logic
                    $idKelas = null;
                    if (!empty($namaKelas)) {
                        $kelas = $findKelasCached($namaKelas, $data['tingkat'] ?? null, $namaWali);
                        if ($kelas) {
                            $idKelas = $kelas->id_kelas;
                        }
                    }

                    // Identifikasi NISN
                    $effectiveNisn = $nisn ?: ($nipNisn ?: ($data['niss'] ?? ''));
                    if (empty($effectiveNisn)) {
                        $effectiveNisn = 'S' . strtoupper(substr(md5($nama . $namaKelas), 0, 9));
                    }

                    // Cari siswa via memory cache
                    $idSiswa = $existingSiswa[$effectiveNisn] ?? null;
                    $siswa = $idSiswa ? Siswa::find($idSiswa) : null;
                    if (!$siswa && $idKelas) {
                        $siswa = Siswa::where('nama', $nama)->where('id_kelas', $idKelas)->first();
                    }

                    if (!$siswa) {
                        $userUname = $username ?: 'siswa.' . preg_replace('/[^a-zA-Z0-9]/', '', $effectiveNisn);
                        $userUname = substr($userUname, 0, 26);
                        if (isset($existingUsernames[$userUname])) {
                            $userUname .= rand(100, 999);
                        }

                        $hashedPassword = ($password === $defaultPassword || empty($password)) ? $defaultPasswordHash : Hash::make($password);

                        $user = User::create([
                            'nama' => $nama,
                            'username' => $userUname,
                            'password' => $hashedPassword,
                            'role' => 'ortu',
                            'no_hp' => $noHp ?: null,
                            'aktif' => 1,
                        ]);
                        $existingUsernames[$userUname] = $user->id_user;

                        $newSiswa = Siswa::create([
                            'id_user' => $user->id_user,
                            'nama' => $nama,
                            'nisn' => $effectiveNisn,
                            'id_kelas' => $idKelas,
                            'jenis_kelamin' => $jenisKelamin,
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
            }

            DB::commit();
            return back()->with('success', "Proses import CSV selesai: {$inserted} data baru berhasil ditambahkan, {$updated} data diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan pada baris ' . $rowNum . ': ' . $e->getMessage());
        }
    }

    public function downloadTemplate($type)
    {
        $filePath = public_path('csv/' . $type);
        if (!file_exists($filePath)) {
            if ($type === 'template_master') {
                $filePath = public_path('csv/template_master_import_sekolah.csv');
            } elseif ($type === 'sk_wali_kelas') {
                $filePath = public_path('csv/sk_wali_kelas_2026_2027.csv');
            } elseif ($type === 'sk_guru') {
                $filePath = public_path('csv/sk_pembagian_tugas_mengajar_2026_2027.csv');
            }
        }

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return back()->with('error', 'File template tidak ditemukan.');
    }
}
