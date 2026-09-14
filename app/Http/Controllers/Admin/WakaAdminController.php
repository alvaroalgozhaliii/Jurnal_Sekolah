<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use App\Models\TahunPelajaran;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class WakaAdminController extends Controller
{
    public static function getWakaRoles(): array
    {
        return [
            'kepala_sekolah' => [
                'role_key'    => 'kepala_sekolah',
                'title'       => 'Kepala Sekolah',
                'bidang'      => 'Pimpinan Satuan Pendidikan',
                'tugas'       => 'Penanggung jawab seluruh kegiatan KBM, manajerial, supervisi, dan persetujuan akhir pengajuan izin/dispensasi.',
                'color'       => '#7c3aed',
                'bg_light'    => 'rgba(124, 58, 237, 0.1)',
                'icon'        => 'award',
                'sk_nip'      => '19810115 200312 1 003',
                'sk_nama'     => 'Trisno Wibowo, S.Pd, M.M',
            ],
            'waka_kurikulum' => [
                'role_key'    => 'waka_kurikulum',
                'title'       => 'Waka Bidang Kurikulum',
                'bidang'      => 'Kurikulum & Pembelajaran',
                'tugas'       => 'Manajemen kurikulum KBM, kalender akademik, pembagian jadwal mengajar, dan pengelolaan jadwal piket waka.',
                'color'       => '#2563eb',
                'bg_light'    => 'rgba(37, 99, 235, 0.1)',
                'icon'        => 'book-open',
                'sk_nip'      => '19820822 201407 2 002',
                'sk_nama'     => 'Hardini Indahing Budi, S.E., M.Pd.',
            ],
            'waka_kesiswaan' => [
                'role_key'    => 'waka_kesiswaan',
                'title'       => 'Waka Bidang Kesiswaan',
                'bidang'      => 'Kesiswaan & Kedisiplinan',
                'tugas'       => 'Pembinaan karakter siswa, OSIS, ekstrakurikuler, ketertiban siswa, serta persetujuan dispensasi & izin siswa.',
                'color'       => '#059669',
                'bg_light'    => 'rgba(5, 150, 105, 0.1)',
                'icon'        => 'users',
                'sk_nip'      => '19780810 202321 1 005',
                'sk_nama'     => 'Fajar Luthfianto, S.Pd',
            ],
            'waka_sdm' => [
                'role_key'    => 'waka_sdm',
                'title'       => 'Waka Bidang SDM / Ketenagaan',
                'bidang'      => 'Ketenagaan & Kepegawaian',
                'tugas'       => 'Pengelolaan dan pembinaan guru & staf tenaga kependidikan, serta persetujuan izin dinas/keperluan guru.',
                'color'       => '#0891b2',
                'bg_light'    => 'rgba(8, 145, 178, 0.1)',
                'icon'        => 'user-check',
                'sk_nip'      => '19721030 200312 1 002',
                'sk_nama'     => 'Setiyo Winarko, S.Pd',
            ],
            'waka_sarpras' => [
                'role_key'    => 'waka_sarpras',
                'title'       => 'Waka Bidang Sarana & Prasarana',
                'bidang'      => 'Sarana, Prasarana & Fasilitas',
                'tugas'       => 'Pengelolaan sarana gedung, laboratorium, fasilitas KBM sekolah, inventaris, dan pemeliharaan sarana.',
                'color'       => '#d97706',
                'bg_light'    => 'rgba(217, 119, 6, 0.1)',
                'icon'        => 'tool',
                'sk_nip'      => '19771112 202221 1 007',
                'sk_nama'     => 'Hendro Suwignyo, ST',
            ],
            'waka_humas' => [
                'role_key'    => 'waka_humas',
                'title'       => 'Waka Bidang Hubungan Masyarakat',
                'bidang'      => 'Humas & Kemitraan Industri',
                'tugas'       => 'Kerjasama dunia usaha/dunia industri (DUDI), penelusuran tamatan (BKK), PKL/Prakerin, dan komunikasi publik.',
                'color'       => '#db2777',
                'bg_light'    => 'rgba(219, 39, 119, 0.1)',
                'icon'        => 'globe',
                'sk_nip'      => '19820303 200901 2 009',
                'sk_nama'     => 'Niken Hari Pratiwi, S.Psi.,M.Pd',
            ],
        ];
    }

    public function index(Request $request)
    {
        $wakaRoles = self::getWakaRoles();
        $assignedPejabat = [];

        foreach ($wakaRoles as $roleKey => $meta) {
            // Cari user pejabat yang aktif dengan role tersebut dan terhubung ke guru
            $user = User::with('guru')
                ->where('role', $roleKey)
                ->where('aktif', 1)
                ->whereHas('guru')
                ->first();

            // Fallback jika belum terhubung ke guru, ambil user biasa
            if (!$user) {
                $user = User::with('guru')->where('role', $roleKey)->where('aktif', 1)->first();
            }

            $assignedPejabat[$roleKey] = [
                'meta' => $meta,
                'user' => $user,
                'guru' => $user?->guru,
            ];
        }

        // Baca seluruh data SK Tugas Tambahan dari CSV jika ada
        $skPersonil = $this->loadSkTugasTambahan();

        $search = $request->get('search');
        if ($search) {
            $skPersonil = array_filter($skPersonil, function ($item) use ($search) {
                return stripos($item['nama'], $search) !== false
                    || stripos($item['nip'], $search) !== false
                    || stripos($item['jabatan'], $search) !== false;
            });
        }

        $guruList = Guru::with('user')->orderBy('nama', 'asc')->get();
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first() ?? (object)['tahun' => '2026/2027', 'semester' => 'Ganjil'];

        $totalWaka = 5;
        $totalAssignedWaka = 0;
        foreach (['waka_kurikulum', 'waka_kesiswaan', 'waka_sdm', 'waka_sarpras', 'waka_humas'] as $rk) {
            if (!empty($assignedPejabat[$rk]['user'])) {
                $totalAssignedWaka++;
            }
        }

        $isKepalaAssigned = !empty($assignedPejabat['kepala_sekolah']['user']);

        return view('admin.waka.index', compact(
            'wakaRoles',
            'assignedPejabat',
            'skPersonil',
            'guruList',
            'tahunAktif',
            'totalWaka',
            'totalAssignedWaka',
            'isKepalaAssigned',
            'search'
        ));
    }

    public function update(Request $request, string $roleKey)
    {
        $validRoles = array_keys(self::getWakaRoles());
        if (!in_array($roleKey, $validRoles)) {
            return back()->with('error', 'Jabatan / Peran tidak valid.');
        }

        $request->validate([
            'id_guru' => 'nullable|exists:guru,id_guru',
            'no_hp'   => 'nullable|string|max:30',
        ]);

        $wakaRoles = self::getWakaRoles();
        $roleTitle = $wakaRoles[$roleKey]['title'];

        DB::beginTransaction();
        try {
            if ($request->filled('id_guru')) {
                $guru = Guru::findOrFail($request->id_guru);

                // 1. Jika guru lama memegang peran ini, kembalikan ke 'guru'
                $currentUsers = User::where('role', $roleKey)->get();
                foreach ($currentUsers as $oldUser) {
                    if ($oldUser->id_user !== $guru->id_user) {
                        $isWali = DB::table('kelas')->where('id_guru_walikelas', $oldUser->guru?->id_guru)->exists();
                        $oldUser->update([
                            'role' => $isWali ? 'wali_kelas' : 'guru',
                        ]);
                    }
                }

                // 2. Jika guru target belum memiliki user account, buatkan
                $user = $guru->user;
                if (!$user) {
                    $cleanNip = preg_replace('/\D/', '', $guru->nip ?: 'guru' . $guru->id_guru);
                    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(',', $guru->nama)[0])) ?: 'guru' . $guru->id_guru;
                    
                    $origUser = $username;
                    $c = 1;
                    while (User::where('username', $username)->exists()) {
                        $username = $origUser . $c++;
                    }

                    $user = User::create([
                        'nama'     => $guru->nama,
                        'nip'      => $guru->nip,
                        'nik'      => $guru->nik,
                        'username' => $username,
                        'password' => \Illuminate\Support\Facades\Hash::make('guru123'),
                        'role'     => $roleKey,
                        'no_hp'    => $request->no_hp ?: $guru->no_telp,
                        'aktif'    => 1,
                    ]);

                    $guru->update(['id_user' => $user->id_user]);
                } else {
                    $cleanNip = $guru->nip ?: $user->nip;
                    $user->update([
                        'role'  => $roleKey,
                        'nip'   => $cleanNip,
                        'aktif' => 1,
                        'no_hp' => $request->no_hp ?: ($user->no_hp ?: $guru->no_telp),
                    ]);
                }

                if ($request->filled('no_hp')) {
                    $guru->update(['no_telp' => $request->no_hp]);
                }

                DB::commit();
                return back()->with('success', "Penugasan {$roleTitle} berhasil diperbarui: {$guru->nama}.");
            } else {
                // Kosongkan penugasan
                $currentUsers = User::where('role', $roleKey)->get();
                foreach ($currentUsers as $oldUser) {
                    $isWali = DB::table('kelas')->where('id_guru_walikelas', $oldUser->guru?->id_guru)->exists();
                    $oldUser->update([
                        'role' => $isWali ? 'wali_kelas' : 'guru',
                    ]);
                }

                DB::commit();
                return back()->with('success', "Penugasan {$roleTitle} berhasil dikosongkan.");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui penugasan: ' . $e->getMessage());
        }
    }

    public function syncFromSk()
    {
        $skAssignments = self::getWakaRoles();
        $syncedCount = 0;

        DB::beginTransaction();
        try {
            // Nonaktifkan dummy seed accounts agar tidak tumpang tindih
            User::whereIn('username', ['waka.kurikulum', 'waka.kesiswaan', 'waka.sdm', 'waka.sarpras', 'waka.humas'])
                ->whereDoesntHave('guru')
                ->update(['aktif' => 0]);

            foreach ($skAssignments as $roleKey => $info) {
                $cleanNip = preg_replace('/\D/', '', $info['sk_nip']);
                
                $guru = Guru::whereRaw("REPLACE(REPLACE(nip, ' ', ''), '-', '') = ?", [$cleanNip])->first();
                if (!$guru) {
                    $guru = Guru::where('nama', 'like', '%' . explode(',', $info['sk_nama'])[0] . '%')->first();
                }

                if ($guru) {
                    $user = $guru->user;
                    if ($user) {
                        $user->update([
                            'role'  => $roleKey,
                            'nip'   => $guru->nip,
                            'aktif' => 1,
                        ]);
                        $syncedCount++;
                    }
                }
            }

            DB::commit();
            return back()->with('success', "Sinkronisasi berhasil! {$syncedCount} pejabat Waka & Kepala Sekolah telah terhubung dengan data SK 2026/2027.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal sinkronisasi SK: ' . $e->getMessage());
        }
    }

    public function exportCsv()
    {
        $wakaRoles = self::getWakaRoles();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data_waka_pejabat_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($wakaRoles) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
            fputcsv($file, ['No', 'Jabatan', 'Bidang', 'Nama Pejabat / Guru', 'NIP', 'No Telepon', 'Status Penugasan', 'Deskripsi Tugas']);

            $no = 1;
            foreach ($wakaRoles as $roleKey => $meta) {
                $user = User::with('guru')->where('role', $roleKey)->where('aktif', 1)->whereHas('guru')->first()
                     ?? User::with('guru')->where('role', $roleKey)->where('aktif', 1)->first();

                $guru = $user?->guru;

                fputcsv($file, [
                    $no++,
                    $meta['title'],
                    $meta['bidang'],
                    $guru?->nama ?? ($user?->nama ?? 'Belum Ditugaskan'),
                    $guru?->nip ?? ($user?->nip ?? '-'),
                    $user?->no_hp ?? ($guru?->no_telp ?? '-'),
                    $user ? 'Aktif' : 'Kosong',
                    $meta['tugas'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function loadSkTugasTambahan(): array
    {
        $csvPath = public_path('csv/sk_tugas_tambahan_2026_2027.csv');
        if (!file_exists($csvPath)) {
            return [];
        }

        $rows = [];
        if (($handle = fopen($csvPath, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) >= 4) {
                    $rows[] = [
                        'no'              => trim($data[0] ?? ''),
                        'nama'            => trim($data[1] ?? ''),
                        'nip'             => trim($data[2] ?? ''),
                        'jabatan'         => trim($data[3] ?? ''),
                        'tahun_pelajaran' => trim($data[4] ?? '2026/2027'),
                    ];
                }
            }
            fclose($handle);
        }

        return $rows;
    }
}