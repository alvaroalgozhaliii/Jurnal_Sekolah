<?php

namespace App\Http\Controllers;

use App\Models\JadwalWaka;
use App\Models\User;
use App\Models\Guru;
use App\Services\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JadwalPiketController extends Controller
{
    /**
     * Tampilan utama manajemen Jadwal Piket (Waka SDM, Waka Kurikulum, Admin).
     */
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $query = JadwalWaka::with(['waka', 'guruPiket'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', "%{$keyword}%")
                  ->orWhereHas('waka', fn($w) => $w->where('nama', 'like', "%{$keyword}%"))
                  ->orWhereHas('guruPiket', fn($g) => $g->where('nama', 'like', "%{$keyword}%"));
            });
        }

        $jadwal = $query->orderBy('tanggal')->paginate(35)->withQueryString();

        $bulanTersedia = JadwalWaka::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun')
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) DESC')
            ->get();

        $wakas = $this->wakas();
        $gurus = $this->gurus();

        return view('waka-kurikulum.index', compact(
            'jadwal', 'wakas', 'gurus',
            'bulan', 'tahun', 'bulanTersedia'
        ));
    }

    /**
     * Form pembuatan jadwal piket bulanan sekaligus.
     */
    public function create()
    {
        $existingDates = JadwalWaka::pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        return view('waka-kurikulum.create', [
            'wakas'         => $this->wakas(),
            'gurus'         => $this->gurus(),
            'existingDates' => $existingDates,
        ]);
    }

    /**
     * Simpan jadwal bulanan.
     */
    public function store(Request $request)
    {
        $hariList = $request->input('hari', []);

        if (empty($hariList)) {
            return back()->withErrors(['hari' => 'Tidak ada hari yang dipilih. Pastikan ada tanggal dalam bulan ini.']);
        }

        $wakaMap = $request->input('id_user_waka', []);
        $guruMap = $request->input('id_guru_piket', []);
        $ketMap  = $request->input('keterangan', []);

        $saved = 0;
        $skippedExisting = 0;

        foreach ($hariList as $tgl) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) continue;

            $wakaId = $wakaMap[$tgl] ?? null;
            $guruId = $guruMap[$tgl] ?? null;

            // Jika Waka dan Guru dua-duanya kosong (misal hari libur), lewati
            if (empty($wakaId) && empty($guruId)) {
                continue;
            }

            // Waka wajib diisi untuk penugasan piket
            if (empty($wakaId)) {
                continue;
            }

            if (JadwalWaka::whereDate('tanggal', $tgl)->exists()) {
                $skippedExisting++;
                continue;
            }

            JadwalWaka::create([
                'tanggal'       => $tgl,
                'id_user_waka'  => $wakaId,
                'id_guru_piket' => !empty($guruId) ? $guruId : null,
                'keterangan'    => $ketMap[$tgl] ?? null,
            ]);
            $saved++;
        }

        if ($saved === 0 && $skippedExisting === 0) {
            return back()->withErrors(['hari' => 'Silakan pilih setidaknya 1 Waka Bertugas untuk tanggal yang ingin dijadwalkan.']);
        }

        $msg = "Jadwal piket bulanan berhasil disimpan: {$saved} hari penugasan aktif telah ditambahkan.";
        if ($skippedExisting > 0) {
            $msg .= " ({$skippedExisting} hari dilewati karena sudah ada jadwal sebelumnya.)";
        }

        return redirect()->route('jadwal-piket.index')->with('success', $msg);
    }

    /**
     * Form edit jadwal piket per hari.
     */
    public function edit($id)
    {
        $jadwalWaka = JadwalWaka::findOrFail($id);
        return view('waka-kurikulum.edit', [
            'jadwalWaka' => $jadwalWaka,
            'wakas'      => $this->wakas(),
            'gurus'      => $this->gurus(),
        ]);
    }

    /**
     * Update jadwal piket per hari.
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalWaka::findOrFail($id);
        $jadwal->update($this->validated($request, (int) $id));
        return redirect()->route('jadwal-piket.index')->with('success', 'Jadwal Piket & Waka Bertugas berhasil diperbarui.');
    }

    /**
     * Hapus jadwal piket.
     */
    public function destroy($id)
    {
        JadwalWaka::findOrFail($id)->delete();
        return redirect()->route('jadwal-piket.index')->with('success', 'Jadwal Piket & Waka Bertugas berhasil dihapus.');
    }

    /**
     * Download Template CSV untuk Jadwal Piket.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_jadwal_piket.csv"',
        ];

        // Ambil contoh waka dan guru aktif jika ada
        $sampleWaka = User::whereIn('role', [
            'waka_sdm', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas'
        ])->first();
        $sampleGuru = Guru::first();

        $wakaName = $sampleWaka ? $sampleWaka->nama : 'Nama Waka / Username';
        $guruName = $sampleGuru ? $sampleGuru->nama : 'Nama Guru Piket / NIP';

        $sample = [
            ['tanggal', 'waka', 'guru_piket', 'keterangan'],
            [date('Y-m-01'), $wakaName, $guruName, 'Piket Pagi - Siang'],
            [date('Y-m-02'), $wakaName, $guruName, 'Piket Reguler'],
            [date('Y-m-03'), $wakaName, $guruName, 'Piket Pengawasan KBM'],
        ];

        return response()->stream(function () use ($sample) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            foreach ($sample as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, $headers);
    }

    /**
     * Export Data Jadwal Piket ke CSV.
     */
    public function exportCsv(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));

        $query = JadwalWaka::with(['waka', 'guruPiket']);

        if ($bulan) {
            $query->whereMonth('tanggal', (int) $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', (int) $tahun);
        }

        $items = $query->orderBy('tanggal')->get();

        $fileName = 'jadwal_piket_' . ($bulan ? "bulan_{$bulan}_" : '') . "tahun_{$tahun}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($items) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($output, [
                'No',
                'Tanggal',
                'Hari',
                'Waka Bertugas',
                'Jabatan Waka',
                'No HP Waka',
                'Guru Piket',
                'NIP Guru Piket',
                'Bidang Studi',
                'Keterangan'
            ]);

            foreach ($items as $index => $item) {
                $tgl = $item->tanggal ? Carbon::parse($item->tanggal) : null;
                fputcsv($output, [
                    $index + 1,
                    $tgl ? $tgl->format('Y-m-d') : '-',
                    $tgl ? $tgl->locale('id')->isoFormat('dddd') : '-',
                    $item->waka->nama ?? '-',
                    $item->waka ? strtoupper(str_replace('_', ' ', $item->waka->role)) : '-',
                    $item->waka->no_hp ?? '-',
                    $item->guruPiket->nama ?? '-',
                    $item->guruPiket->nip ?? '-',
                    $item->guruPiket->bidang_studi ?? '-',
                    $item->keterangan ?? '-',
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Upload & Import CSV Jadwal Piket.
     * Mendukung format CSV standar (tanggal, waka, guru_piket) maupun
     * format jadwal sekolah asli (No, Hari/Tanggal, Petugas Piket KBM Pagi, dst.)
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:10240',
        ]);

        try {
            $parsed = CsvImportService::parseCsv($request->file('csv_file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses file CSV: ' . $e->getMessage());
        }

        $wakas = $this->wakas();
        $gurus = Guru::all();

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        // Mapping bulan Indonesia
        $indonesianMonths = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'agt' => 8,
            'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
        ];

        // Helper: cocokkan nama Waka ke User
        $matchWaka = function (string $input) use ($wakas): ?object {
            $input = trim($input);
            if (empty($input)) return null;
            // Exact match
            $found = $wakas->first(fn($w) =>
                (string) $w->id_user === $input
                || strcasecmp($w->username ?? '', $input) === 0
                || ($w->nip && $w->nip === $input)
                || strcasecmp($w->nama, $input) === 0
            );
            if ($found) return $found;

            // Cari nama depan - ambil 2 kata pertama dari input (tanpa gelar)
            $cleanInput = trim(preg_replace('/[,.].*/', '', $input));
            $tokensInput = array_values(array_filter(explode(' ', $cleanInput)));

            $found = $wakas->first(function ($w) use ($input, $cleanInput, $tokensInput) {
                $cleanW = trim(preg_replace('/[,.].*/', '', $w->nama));
                $tokensW = array_values(array_filter(explode(' ', $cleanW)));

                if (stripos($w->nama, $cleanInput) !== false || stripos($input, $cleanW) !== false) {
                    return true;
                }
                // Cocokkan 2 token pertama (toleran typo pada token ke-2)
                if (count($tokensInput) >= 2 && count($tokensW) >= 2) {
                    if (strcasecmp($tokensInput[0], $tokensW[0]) === 0) {
                        similar_text($tokensInput[1], $tokensW[1], $pct);
                        return $pct >= 70;
                    }
                }
                return false;
            });
            return $found;
        };

        // Helper: cocokkan nama Guru ke model Guru
        $matchGuru = function (string $input) use ($gurus): ?object {
            $input = trim($input);
            if (empty($input)) return null;
            $found = $gurus->first(fn($g) =>
                (string) $g->id_guru === $input
                || ($g->nip && $g->nip === $input)
                || strcasecmp($g->nama, $input) === 0
                || stripos($g->nama, $input) !== false
                || stripos($input, $g->nama) !== false
            );
            return $found;
        };

        foreach ($parsed['rows'] as $rowIndex => $data) {
            // ─────────────────────────────────────────────
            // 1. Parse Tanggal
            //    Coba berbagai nama kolom yang mungkin digunakan
            // ─────────────────────────────────────────────
            $rawTanggal = trim(
                $data['tanggal']    ??
                $data['tgl']        ??
                $data['date']       ??
                $data['hari_tanggal'] ??
                $data['hari_dan_tanggal'] ??
                ''
            );

            if (empty($rawTanggal)) {
                $skipped++;
                continue;
            }

            $parsedDate = null;
            try {
                // Format ISO: 2026-09-01
                if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $rawTanggal)) {
                    $parsedDate = Carbon::createFromFormat('Y-m-d', $rawTanggal)->format('Y-m-d');

                // Format d/m/Y: 01/09/2026
                } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $rawTanggal)) {
                    $parsedDate = Carbon::createFromFormat('d/m/Y', $rawTanggal)->format('Y-m-d');

                // Format d-m-Y: 01-09-2026
                } elseif (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $rawTanggal)) {
                    $parsedDate = Carbon::createFromFormat('d-m-Y', $rawTanggal)->format('Y-m-d');

                // Format Indonesia: "Selasa, 1 September 2026" atau "1 September 2026"
                } elseif (preg_match('/(\d{1,2})\s+([a-zA-Z\']+)\s+(\d{4})/', $rawTanggal, $m)) {
                    $day   = (int) $m[1];
                    $month = $indonesianMonths[strtolower($m[2])] ?? null;
                    $year  = (int) $m[3];
                    if ($month && $day > 0 && $year > 2000) {
                        $parsedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    } else {
                        throw new \Exception("Bulan tidak dikenali: {$m[2]}");
                    }

                } else {
                    // Fallback ke Carbon::parse
                    $parsedDate = Carbon::parse($rawTanggal)->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                $errors[] = "Baris " . ($rowIndex + 2) . ": Format tanggal '{$rawTanggal}' tidak valid — {$e->getMessage()}";
                $skipped++;
                continue;
            }

            // ─────────────────────────────────────────────
            // 2. Cari Waka (dari kolom piket_waka, waka, dll)
            // ─────────────────────────────────────────────
            $wakaInput = trim(
                $data['piket_waka']  ??
                $data['waka']        ??
                $data['nama_waka']   ??
                $data['waka_bertugas'] ??
                $data['id_user_waka'] ??
                ''
            );
            $matchedWaka = $matchWaka($wakaInput);

            // Fallback ke waka SDM pertama yang aktif jika tidak ketemu
            if (!$matchedWaka) {
                $matchedWaka = $wakas->first();
            }

            if (!$matchedWaka) {
                $errors[] = "Baris " . ($rowIndex + 2) . ": Tidak ada akun Waka aktif di sistem.";
                $skipped++;
                continue;
            }

            // ─────────────────────────────────────────────
            // 3. Cari Guru Piket (opsional — kolom guru_piket, koordinator_piket_kbm_pagi, dsb)
            // ─────────────────────────────────────────────
            $guruInput = trim(
                $data['guru_piket']                   ??
                $data['guru']                          ??
                $data['nama_guru']                     ??
                $data['koordinator_piket_kbm_pagi']   ??
                $data['koordinator_pagi']              ??
                $data['id_guru_piket']                 ??
                ''
            );
            $matchedGuru = $guruInput ? $matchGuru($guruInput) : null;

            // ─────────────────────────────────────────────
            // 4. Simpan field detail piket (format jadwal asli sekolah)
            // ─────────────────────────────────────────────
            $petugasPagi     = trim($data['petugas_piket_kbm_pagi']      ?? $data['petugas_pagi']     ?? '') ?: null;
            $koordinatorPagi = trim($data['koordinator_piket_kbm_pagi']  ?? $data['koordinator_pagi'] ?? '') ?: null;
            $petugasSiang    = trim($data['petugas_piket_kbm_siang']     ?? $data['petugas_siang']    ?? '') ?: null;
            $koordinatorSiang= trim($data['koordinator_piket_kbm_siang'] ?? $data['koordinator_siang']?? '') ?: null;

            $keterangan = trim($data['keterangan'] ?? $data['ket'] ?? $data['catatan'] ?? '') ?: null;

            // ─────────────────────────────────────────────
            // 5. Simpan atau Update
            // ─────────────────────────────────────────────
            $existing = JadwalWaka::whereDate('tanggal', $parsedDate)->first();
            if ($existing) {
                $existing->update([
                    'id_user_waka'     => $matchedWaka->id_user,
                    'id_guru_piket'    => $matchedGuru ? $matchedGuru->id_guru : $existing->id_guru_piket,
                    'petugas_pagi'     => $petugasPagi     ?? $existing->petugas_pagi,
                    'koordinator_pagi' => $koordinatorPagi ?? $existing->koordinator_pagi,
                    'petugas_siang'    => $petugasSiang    ?? $existing->petugas_siang,
                    'koordinator_siang'=> $koordinatorSiang?? $existing->koordinator_siang,
                    'keterangan'       => $keterangan      ?? $existing->keterangan,
                ]);
                $updated++;
            } else {
                JadwalWaka::create([
                    'tanggal'          => $parsedDate,
                    'id_user_waka'     => $matchedWaka->id_user,
                    'id_guru_piket'    => $matchedGuru?->id_guru,
                    'petugas_pagi'     => $petugasPagi,
                    'koordinator_pagi' => $koordinatorPagi,
                    'petugas_siang'    => $petugasSiang,
                    'koordinator_siang'=> $koordinatorSiang,
                    'keterangan'       => $keterangan,
                ]);
                $inserted++;
            }
        }

        $message = "Import CSV selesai: {$inserted} jadwal baru ditambahkan, {$updated} jadwal diperbarui.";
        if ($skipped > 0) {
            $message .= " ({$skipped} baris dilewati).";
        }

        if (!empty($errors)) {
            return back()->with('success', $message)->withErrors($errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Tampilan Jadwal Piket untuk Akun Guru & Wali Kelas.
     */
    public function viewGuru(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $todayDate = Carbon::today()->toDateString();
        $user = Auth::user();
        $guru = $user->guru ?: Guru::where('nama', $user->nama)->orWhere('nip', $user->nip)->first();

        $query = JadwalWaka::with(['waka', 'guruPiket'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', "%{$keyword}%")
                  ->orWhereHas('waka', fn($w) => $w->where('nama', 'like', "%{$keyword}%"))
                  ->orWhereHas('guruPiket', fn($g) => $g->where('nama', 'like', "%{$keyword}%"));
            });
        }

        $jadwal = $query->orderBy('tanggal')->paginate(35)->withQueryString();

        $piketHariIni = JadwalWaka::with(['waka', 'guruPiket'])->whereDate('tanggal', $todayDate)->first();

        $bulanTersedia = JadwalWaka::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun')
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) DESC')
            ->get();

        return view('guru.jadwal-piket', compact(
            'jadwal', 'piketHariIni', 'bulan', 'tahun', 'bulanTersedia', 'guru'
        ));
    }

    /**
     * Tampilan Jadwal Piket untuk Akun Petugas Piket.
     */
    public function viewPiket(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $todayDate = Carbon::today()->toDateString();

        $query = JadwalWaka::with(['waka', 'guruPiket'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', "%{$keyword}%")
                  ->orWhereHas('waka', fn($w) => $w->where('nama', 'like', "%{$keyword}%"))
                  ->orWhereHas('guruPiket', fn($g) => $g->where('nama', 'like', "%{$keyword}%"));
            });
        }

        $jadwal = $query->orderBy('tanggal')->paginate(35)->withQueryString();

        $piketHariIni = JadwalWaka::with(['waka', 'guruPiket'])->whereDate('tanggal', $todayDate)->first();

        $bulanTersedia = JadwalWaka::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun')
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByRaw('YEAR(tanggal) DESC, MONTH(tanggal) DESC')
            ->get();

        return view('piket.jadwal', compact(
            'jadwal', 'piketHariIni', 'bulan', 'tahun', 'bulanTersedia'
        ));
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $uniqueTanggal = 'unique:jadwal_waka,tanggal' . ($id ? ',' . $id . ',id_jadwal_waka' : '');

        return $request->validate([
            'tanggal'       => ['required', 'date', $uniqueTanggal],
            'id_user_waka'  => [
                'required', 'integer',
                Rule::exists('users', 'id_user')->where(fn ($q) => $q
                    ->whereIn('role', ['waka_kurikulum','waka_kesiswaan','waka_sdm','waka_sarpras','waka_humas'])
                    ->where('aktif', 1)),
            ],
            'id_guru_piket' => ['nullable', 'integer', 'exists:guru,id_guru'],
            'keterangan'    => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function wakas()
    {
        return User::whereIn('role', [
            'waka_kurikulum', 'waka_kesiswaan', 'waka_sdm', 'waka_sarpras', 'waka_humas',
        ])->where('aktif', 1)->orderBy('nama')->get();
    }

    private function gurus()
    {
        return Guru::orderBy('nama')->get();
    }
}
