<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Services\KbmService;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tingkatFilter = $request->get('tingkat');
        $viewMode = $request->get('view', 'folder'); // 'folder' (default) or 'table'

        // Query untuk daftar kelas (tampilan folder/kolom kelas)
        $kelasQuery = Kelas::with(['jurusan', 'jadwal.guru'])
            ->withCount('jadwal')
            ->withCount('siswa');

        if ($tingkatFilter) {
            $kelasQuery->where('tingkat', $tingkatFilter);
        }

        if ($search) {
            $kelasQuery->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%")
                  ->orWhere('wali_kelas', 'like', "%{$search}%")
                  ->orWhereHas('jurusan', function($qj) use ($search) {
                      $qj->where('nama_jurusan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jadwal', function($qjad) use ($search) {
                      $qjad->where('mapel', 'like', "%{$search}%");
                  });
            });
        }

        $kelasList = $kelasQuery->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        // Query untuk jadwal flat (untuk view mode tabel atau guru)
        $query = Jadwal::with(['kelas', 'guru']);

        if (auth()->user()->isGuru() && auth()->user()->guru) {
            $query->where('id_guru', auth()->user()->guru->id_guru);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mapel', 'like', "%{$search}%")
                  ->orWhere('hari', 'like', "%{$search}%")
                  ->orWhere('ruang', 'like', "%{$search}%")
                  ->orWhere('jam_ke', 'like', "%{$search}%")
                  ->orWhereHas('kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guru', function($qg) use ($search) {
                      $qg->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $jadwal = $query->orderBy('hari', 'asc')->orderBy('jam_ke', 'asc')->get();

        return view('jadwal.index', compact('kelasList', 'jadwal', 'search', 'tingkatFilter', 'viewMode'));
    }

    public function create(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $guru = Guru::orderBy('nama', 'asc')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        $selectedKelasId = $request->get('id_kelas');
        $selectedKelas = $selectedKelasId ? Kelas::find($selectedKelasId) : null;

        return view('jadwal.create', compact('kelas', 'guru', 'mapel', 'selectedKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|integer|min:1|max:13',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'required|exists:guru,id_guru',
            'mapel' => 'required|string|max:150',
            'ruang' => 'nullable|string|max:50',
        ]);

        $hari = $request->hari;
        $jamKe = (int) $request->jam_ke;
        $kelas = Kelas::find($request->id_kelas);
        $tingkat = $kelas ? $kelas->tingkat : null;

        // 1. Validasi alokasi jam KBM
        $waktu = KbmService::getAlokasiWaktu($hari, $jamKe, $tingkat);
        if (!$waktu) {
            if ($hari === 'Jumat' && $jamKe === 13) {
                return back()->withInput()->withErrors([
                    'jam_ke' => "Jam ke-13 pada hari Jumat hanya berlaku untuk Kelas X (10)."
                ]);
            }
            return back()->withInput()->withErrors([
                'jam_ke' => "Pilihan Jam ke-{$jamKe} tidak memiliki alokasi waktu KBM pada hari {$hari}."
            ]);
        }

        // 2. Validasi bentrok kelas
        $bentrokKelas = Jadwal::where('id_kelas', $request->id_kelas)
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->where('aktif', 1)
            ->first();

        if ($bentrokKelas) {
            return back()->withInput()->withErrors([
                'id_kelas' => "Kelas sudah memiliki jadwal pelajaran ({$bentrokKelas->mapel}) pada hari {$hari} Jam ke-{$jamKe}."
            ]);
        }

        // 3. Validasi bentrok guru
        if ($request->id_guru) {
            $bentrokGuru = Jadwal::where('id_guru', $request->id_guru)
                ->where('hari', $hari)
                ->where('jam_ke', $jamKe)
                ->where('aktif', 1)
                ->first();

            if ($bentrokGuru) {
                $namaGuru = $bentrokGuru->guru->nama ?? 'Guru';
                $namaKelas = $bentrokGuru->kelas->nama_kelas ?? 'kelas lain';
                return back()->withInput()->withErrors([
                    'id_guru' => "Guru {$namaGuru} sudah mengajar di {$namaKelas} pada hari {$hari} Jam ke-{$jamKe}."
                ]);
            }
        }

        // 4. Validasi bentrok ruangan
        if (!empty($request->ruang)) {
            $bentrokRuang = Jadwal::where('ruang', $request->ruang)
                ->where('hari', $hari)
                ->where('jam_ke', $jamKe)
                ->where('aktif', 1)
                ->first();

            if ($bentrokRuang) {
                return back()->withInput()->withErrors([
                    'ruang' => "Ruangan {$request->ruang} sudah digunakan oleh kelas {$bentrokRuang->kelas->nama_kelas} pada hari {$hari} Jam ke-{$jamKe}."
                ]);
            }
        }

        Jadwal::create([
            'hari' => $hari,
            'jam_ke' => $jamKe,
            'id_kelas' => $request->id_kelas,
            'id_guru' => $request->id_guru,
            'mapel' => $request->mapel,
            'ruang' => $request->ruang,
            'waktu_mulai' => $waktu['waktu_mulai'],
            'waktu_selesai' => $waktu['waktu_selesai'],
            'aktif' => $request->input('aktif', 1),
        ]);

        // Redirect ke detail kelas jika dibuka dari sana
        if ($request->redirect_to_kelas) {
            return redirect()->route('kelas.show', $request->redirect_to_kelas)
                ->with('success', 'Jadwal pelajaran berhasil ditambahkan');
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $jadwal = Jadwal::with(['kelas', 'guru'])->findOrFail($id);
        return view('jadwal.show', compact('jadwal'));
    }

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $guru = Guru::orderBy('nama', 'asc')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('jadwal.edit', compact('jadwal', 'kelas', 'guru', 'mapel'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|integer|min:1|max:13',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'required|exists:guru,id_guru',
            'mapel' => 'required|string|max:150',
            'ruang' => 'nullable|string|max:50',
        ]);

        $hari = $request->hari;
        $jamKe = (int) $request->jam_ke;
        $kelas = Kelas::find($request->id_kelas);
        $tingkat = $kelas ? $kelas->tingkat : null;

        // 1. Validasi alokasi jam KBM
        $waktu = KbmService::getAlokasiWaktu($hari, $jamKe, $tingkat);
        if (!$waktu) {
            if ($hari === 'Jumat' && $jamKe === 13) {
                return back()->withInput()->withErrors([
                    'jam_ke' => "Jam ke-13 pada hari Jumat hanya berlaku untuk Kelas X (10)."
                ]);
            }
            return back()->withInput()->withErrors([
                'jam_ke' => "Pilihan Jam ke-{$jamKe} tidak memiliki alokasi waktu KBM pada hari {$hari}."
            ]);
        }

        // 2. Validasi bentrok kelas (abaikan id saat ini)
        $bentrokKelas = Jadwal::where('id_kelas', $request->id_kelas)
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->where('id_jadwal', '!=', $id)
            ->where('aktif', 1)
            ->first();

        if ($bentrokKelas) {
            return back()->withInput()->withErrors([
                'id_kelas' => "Kelas sudah memiliki jadwal pelajaran ({$bentrokKelas->mapel}) pada hari {$hari} Jam ke-{$jamKe}."
            ]);
        }

        // 3. Validasi bentrok guru (abaikan id saat ini)
        if ($request->id_guru) {
            $bentrokGuru = Jadwal::where('id_guru', $request->id_guru)
                ->where('hari', $hari)
                ->where('jam_ke', $jamKe)
                ->where('id_jadwal', '!=', $id)
                ->where('aktif', 1)
                ->first();

            if ($bentrokGuru) {
                $namaGuru = $bentrokGuru->guru->nama ?? 'Guru';
                $namaKelas = $bentrokGuru->kelas->nama_kelas ?? 'kelas lain';
                return back()->withInput()->withErrors([
                    'id_guru' => "Guru {$namaGuru} sudah mengajar di {$namaKelas} pada hari {$hari} Jam ke-{$jamKe}."
                ]);
            }
        }

        // 4. Validasi bentrok ruangan (abaikan id saat ini)
        if (!empty($request->ruang)) {
            $bentrokRuang = Jadwal::where('ruang', $request->ruang)
                ->where('hari', $hari)
                ->where('jam_ke', $jamKe)
                ->where('id_jadwal', '!=', $id)
                ->where('aktif', 1)
                ->first();

            if ($bentrokRuang) {
                return back()->withInput()->withErrors([
                    'ruang' => "Ruangan {$request->ruang} sudah digunakan oleh kelas {$bentrokRuang->kelas->nama_kelas} pada hari {$hari} Jam ke-{$jamKe}."
                ]);
            }
        }

        $jadwal->update([
            'hari' => $hari,
            'jam_ke' => $jamKe,
            'id_kelas' => $request->id_kelas,
            'id_guru' => $request->id_guru,
            'mapel' => $request->mapel,
            'ruang' => $request->ruang,
            'waktu_mulai' => $waktu['waktu_mulai'],
            'waktu_selesai' => $waktu['waktu_selesai'],
            'aktif' => $request->input('aktif', $jadwal->aktif),
        ]);

        // Redirect ke detail kelas jika kelas diketahui
        if ($jadwal->id_kelas) {
            return redirect()->route('kelas.show', $jadwal->id_kelas)
                ->with('success', 'Jadwal pelajaran berhasil diubah');
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal pelajaran berhasil diubah');
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

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_jadwal.csv"',
        ];

        $sample = [
            ['nama_kelas', 'hari', 'jam_ke', 'mapel', 'nama_guru', 'ruang', 'waktu_mulai', 'waktu_selesai'],
            ['X RPL 1', 'Senin', 1, 'Bahasa Indonesia', 'Sri Rahayu, S.Pd', 'R. 10', '07:00', '07:40'],
            ['X RPL 1', 'Senin', 2, 'Bahasa Indonesia', 'Sri Rahayu, S.Pd', 'R. 10', '07:40', '08:20'],
            ['X RPL 1', 'Selasa', 1, 'Matematika', 'Arvia Rienetasary, S.Pd', 'Lab. RPL 1', '07:00', '07:40'],
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

        $inserted        = 0;
        $skipDuplikat    = 0;
        $skipKelasNotFound = 0;
        $skipAlokasi     = 0;
        $skipField       = 0;
        $errors          = [];

        // Pre-load semua kelas (beserta tingkat) dan guru agar tidak N+1 query
        $kelasList = Kelas::all()->keyBy('id_kelas');
        $kelasMap  = Kelas::pluck('id_kelas', 'nama_kelas')->toArray();
        $gurus     = Guru::all();

        foreach ($parsed['rows'] as $rowIndex => $data) {
            // Baca kolom dengan berbagai variasi nama (sudah di-lowercase & underscore oleh CsvImportService)
            $namaKelas    = trim($data['nama_kelas']    ?? ($data['kelas']           ?? ($data['nama_kelas_'] ?? '')));
            $hari         = ucfirst(strtolower(trim($data['hari'] ?? '')));
            
            // Dukungan jam tunggal (jam_ke) maupun rentang jam (jam_mulai & jam_selesai)
            $jamMulai     = (int) ($data['jam_mulai']   ?? ($data['jam_ke']          ?? ($data['jam'] ?? ($data['jam_awal'] ?? 0))));
            $jamSelesai   = (int) ($data['jam_selesai'] ?? ($data['jam_akhir']       ?? $jamMulai));
            if ($jamSelesai < $jamMulai) {
                $jamSelesai = $jamMulai;
            }

            $mapel        = trim($data['mapel']         ?? ($data['mata_pelajaran']  ?? ($data['pelajaran']   ?? '')));
            $guruNama     = trim($data['nama_guru']     ?? ($data['guru']            ?? ($data['pengajar']    ?? '')));
            $ruang        = trim($data['ruang']         ?? ($data['ruangan']         ?? '')) ?: null;
            $waktuMulai   = trim($data['waktu_mulai']   ?? ($data['mulai']           ?? '')) ?: null;
            $waktuSelesai = trim($data['waktu_selesai'] ?? ($data['selesai']         ?? '')) ?: null;

            // Validasi field wajib
            if (empty($namaKelas) || empty($hari) || empty($mapel) || $jamMulai <= 0) {
                $skipField++;
                continue;
            }

            // Validasi nama hari
            if (!in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])) {
                $skipField++;
                continue;
            }

            // Cari id_kelas
            $idKelas = $kelasMap[$namaKelas] ?? null;
            if (!$idKelas) {
                $foundKelas = Kelas::where('nama_kelas', 'like', "%{$namaKelas}%")->first();
                if ($foundKelas) {
                    $idKelas = $foundKelas->id_kelas;
                    $kelasList[$idKelas] = $foundKelas;
                }
            }

            if (!$idKelas) {
                $skipKelasNotFound++;
                if (count($errors) < 5) {
                    $errors[] = "Kelas '{$namaKelas}' tidak ditemukan di database.";
                }
                continue;
            }

            // Ambil tingkat kelas untuk validasi alokasi waktu (khusus Jumat jam 13)
            $tingkat = $kelasList[$idKelas]?->tingkat;

            // Match Guru (case-insensitive fuzzy)
            $idGuru = null;
            if (!empty($guruNama)) {
                $cleanSearch = trim(strtok($guruNama, ','));
                $matchedGuru = $gurus->first(function ($g) use ($guruNama, $cleanSearch) {
                    return strcasecmp($g->nama, $guruNama) === 0
                        || stripos($g->nama, $cleanSearch) !== false
                        || stripos($guruNama, trim(strtok($g->nama, ','))) !== false;
                });
                $idGuru = $matchedGuru?->id_guru;
            }

            // Loop untuk setiap jam dalam rentang jam_mulai hingga jam_selesai
            for ($jamKe = $jamMulai; $jamKe <= $jamSelesai; $jamKe++) {
                // Validasi alokasi waktu
                $alokasi = KbmService::getAlokasiWaktu($hari, $jamKe, $tingkat);
                if (!$alokasi) {
                    $skipAlokasi++;
                    if (count($errors) < 5) {
                        $errors[] = "Jam ke-{$jamKe} pada hari {$hari} tidak valid untuk kelas '{$namaKelas}' (tingkat {$tingkat}).";
                    }
                    continue;
                }

                // Auto-fill waktu jika kosong
                $rowWaktuMulai   = $waktuMulai   ?: $alokasi['waktu_mulai'];
                $rowWaktuSelesai = $waktuSelesai ?: $alokasi['waktu_selesai'];

                // Cek duplikat: update jika sudah ada atau buat baru
                $existingJadwal = Jadwal::where('id_kelas', $idKelas)
                    ->where('hari', $hari)
                    ->where('jam_ke', $jamKe)
                    ->first();

                if ($existingJadwal) {
                    $existingJadwal->update([
                        'id_guru'       => $idGuru,
                        'mapel'         => $mapel,
                        'ruang'         => $ruang,
                        'waktu_mulai'   => $rowWaktuMulai,
                        'waktu_selesai' => $rowWaktuSelesai,
                        'aktif'         => 1,
                    ]);
                    $inserted++;
                } else {
                    try {
                        Jadwal::create([
                            'id_kelas'      => $idKelas,
                            'id_guru'       => $idGuru,
                            'hari'          => $hari,
                            'jam_ke'        => $jamKe,
                            'mapel'         => $mapel,
                            'ruang'         => $ruang,
                            'waktu_mulai'   => $rowWaktuMulai,
                            'waktu_selesai' => $rowWaktuSelesai,
                            'aktif'         => 1,
                        ]);
                        $inserted++;
                    } catch (\Throwable $e) {
                        $errors[] = "Gagal menyimpan {$namaKelas} {$hari} jam {$jamKe}: " . $e->getMessage();
                        $skipField++;
                    }
                }
            }
        }

        $totalRows = count($parsed['rows']);
        $parts = [];
        if ($skipDuplikat > 0)      $parts[] = "{$skipDuplikat} sudah ada (duplikat)";
        if ($skipKelasNotFound > 0) $parts[] = "{$skipKelasNotFound} kelas tidak ditemukan";
        if ($skipAlokasi > 0)       $parts[] = "{$skipAlokasi} jam tidak valid";
        if ($skipField > 0)         $parts[] = "{$skipField} data tidak lengkap";

        $totalSkip = $skipDuplikat + $skipKelasNotFound + $skipAlokasi + $skipField;
        $msg = "Import selesai — {$totalRows} baris dibaca, {$inserted} ditambahkan, {$totalSkip} dilewati";
        if (!empty($parts)) {
            $msg .= ' (' . implode(', ', $parts) . ')';
        }
        $msg .= '.';

        // Jika semua baris skip karena field kosong, tampilkan header CSV yang terdeteksi untuk debug
        if ($inserted === 0 && $skipField === $totalRows && $totalRows > 0) {
            $detectedHeaders = implode(', ', $parsed['header']);
            $msg .= " Kolom CSV terdeteksi: [{$detectedHeaders}]. Pastikan ada kolom: nama_kelas, hari, jam_ke, mapel.";
        }

        if (!empty($errors)) {
            $msg .= ' Catatan: ' . implode(' | ', array_slice($errors, 0, 3));
        }

        return back()->with('success', $msg);
    }
}