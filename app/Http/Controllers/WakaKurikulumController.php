<?php

namespace App\Http\Controllers;

use App\Models\JadwalWaka;
use App\Models\User;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class WakaKurikulumController extends Controller
{
    public function dashboard()
    {
        $today = date('Y-m-d');
        $jadwalHariIni = JadwalWaka::with(['waka', 'guruPiket'])->whereDate('tanggal', $today)->first();
        $totalJadwal = JadwalWaka::count();
        $jadwalMendatang = JadwalWaka::with(['waka', 'guruPiket'])
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->take(5)
            ->get();
        $totalPelajaran = Jadwal::where('aktif', 1)->count();
        $wakas = $this->wakas();

        return view('waka-kurikulum.dashboard', compact(
            'jadwalHariIni',
            'totalJadwal',
            'jadwalMendatang',
            'totalPelajaran',
            'wakas'
        ));
    }

    public function index(Request $request)
    {
        // Filter per bulan-tahun
        $bulan  = $request->input('bulan',  date('n'));
        $tahun  = $request->input('tahun',  date('Y'));

        $jadwal = JadwalWaka::with(['waka', 'guruPiket'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal',  $tahun)
            ->orderBy('tanggal')
            ->paginate(35)
            ->withQueryString();

        // Daftar bulan–tahun yang punya data (untuk filter dropdown)
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

    public function create()
    {
        // Kirim daftar tanggal yang sudah ada jadwalnya agar di-skip di tabel
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
     * Simpan jadwal BULANAN sekaligus.
     * Payload: hari[]  (array tanggal), id_user_waka[YYYY-MM-DD], id_guru_piket[YYYY-MM-DD], keterangan[YYYY-MM-DD]
     */
    public function store(Request $request)
    {
        $hariList = $request->input('hari', []);

        if (empty($hariList)) {
            return back()->withErrors(['hari' => 'Tidak ada hari yang dipilih. Pastikan ada tanggal baru dalam bulan ini.']);
        }

        $wakaMap    = $request->input('id_user_waka',   []);
        $guruMap    = $request->input('id_guru_piket',  []);
        $ketMap     = $request->input('keterangan',     []);

        $saved   = 0;
        $skippedExisting = 0;

        foreach ($hariList as $tgl) {
            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) continue;

            $wakaId = $wakaMap[$tgl] ?? null;
            
            // Jika Waka tidak dipilih (misal hari libur/Sabtu/Minggu), lewati tanggal ini (opsional)
            if (empty($wakaId)) {
                continue;
            }

            // Lewati jika tanggal ini sudah ada di database sebelumnya
            if (JadwalWaka::whereDate('tanggal', $tgl)->exists()) {
                $skippedExisting++;
                continue;
            }

            JadwalWaka::create([
                'tanggal'       => $tgl,
                'id_user_waka'  => $wakaId,
                'id_guru_piket' => ($guruMap[$tgl] ?? null) ?: null,
                'keterangan'    => $ketMap[$tgl] ?? null,
            ]);
            $saved++;
        }

        if ($saved === 0 && $skippedExisting === 0) {
            return back()->withErrors(['hari' => 'Silakan pilih setidaknya 1 Waka Bertugas untuk tanggal yang ingin dijadwalkan.']);
        }

        $msg = "Jadwal piket bulanan berhasil disimpan: {$saved} hari penugasan aktif telah ditambahkan.";
        if ($skippedExisting > 0) $msg .= " ({$skippedExisting} hari dilewati karena sudah pernah dibuat.)";

        return redirect()->route('waka-kurikulum.index')->with('success', $msg);
    }

    public function show($id)
    {
        $jadwalWaka = JadwalWaka::with(['waka', 'guruPiket'])->findOrFail($id);
        return view('waka-kurikulum.show', compact('jadwalWaka'));
    }

    public function edit($id)
    {
        $jadwalWaka = JadwalWaka::findOrFail($id);
        return view('waka-kurikulum.edit', [
            'jadwalWaka' => $jadwalWaka,
            'wakas'      => $this->wakas(),
            'gurus'      => $this->gurus(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalWaka::findOrFail($id);
        $jadwal->update($this->validated($request, $id));
        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Piket & Waka Bertugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JadwalWaka::findOrFail($id)->delete();
        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Piket & Waka Bertugas berhasil dihapus.');
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