<?php

namespace App\Http\Controllers;

use App\Models\JadwalWaka;
use App\Models\User;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function index()
    {
        $jadwal = JadwalWaka::with(['waka', 'guruPiket'])->orderBy('tanggal', 'desc')->paginate(15);
        $wakas = $this->wakas();
        $gurus = $this->gurus();

        return view('waka-kurikulum.index', compact('jadwal', 'wakas', 'gurus'));
    }

    public function create()
    {
        return view('waka-kurikulum.create', [
            'wakas' => $this->wakas(),
            'gurus' => $this->gurus(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        JadwalWaka::create($data);

        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Piket & Waka Bertugas berhasil dibuat.');
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
            'wakas' => $this->wakas(),
            'gurus' => $this->gurus(),
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
            'tanggal' => ['required', 'date', $uniqueTanggal],
            'id_user_waka' => [
                'required',
                'integer',
                Rule::exists('users', 'id_user')->where(fn ($query) => $query
                    ->whereIn('role', [
                        'waka_kurikulum',
                        'waka_kesiswaan',
                        'waka_sdm',
                        'waka_sarpras',
                        'waka_humas'
                    ])
                    ->where('aktif', 1)),
            ],
            'id_guru_piket' => [
                'nullable',
                'integer',
                'exists:guru,id_guru',
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function wakas()
    {
        return User::whereIn('role', [
            'waka_kurikulum',
            'waka_kesiswaan',
            'waka_sdm',
            'waka_sarpras',
            'waka_humas'
        ])
        ->where('aktif', 1)
        ->orderBy('nama')
        ->get();
    }

    private function gurus()
    {
        return Guru::orderBy('nama')->get();
    }
}