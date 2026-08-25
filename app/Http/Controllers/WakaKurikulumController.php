<?php

namespace App\Http\Controllers;

use App\Models\JadwalWaka;
use App\Models\User;
use Illuminate\Http\Request;

class WakaKurikulumController extends Controller
{
    public function index()
    {
        $jadwal = JadwalWaka::with('waka')
            ->orderBy('tanggal')
            ->get();
        $wakas = User::whereIn('role', ['waka_sdm', 'waka_kesiswaan', 'waka_kurikulum'])
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get();

        return view('waka-kurikulum.dashboard', compact('jadwal', 'wakas'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        JadwalWaka::create($data);

        return redirect()->route('waka-kurikulum.dashboard')->with('success', 'Jadwal Waka berhasil dibuat.');
    }

    public function edit($id)
    {
        $jadwalEdit = JadwalWaka::findOrFail($id);
        $jadwal = JadwalWaka::with('waka')->orderBy('tanggal')->get();
        $wakas = User::whereIn('role', ['waka_sdm', 'waka_kesiswaan', 'waka_kurikulum'])
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get();

        return view('waka-kurikulum.dashboard', compact('jadwal', 'wakas', 'jadwalEdit'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalWaka::findOrFail($id);
        $jadwal->update($this->validated($request, $id));

        return redirect()->route('waka-kurikulum.dashboard')->with('success', 'Jadwal Waka berhasil diubah.');
    }

    public function destroy($id)
    {
        JadwalWaka::findOrFail($id)->delete();

        return redirect()->route('waka-kurikulum.dashboard')->with('success', 'Jadwal Waka berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $uniqueTanggal = 'unique:jadwal_waka,tanggal' . ($id ? ',' . $id . ',id_jadwal_waka' : '');

        return $request->validate([
            'tanggal' => ['required', 'date', $uniqueTanggal],
            'id_user_waka' => ['required', 'integer', 'exists:users,id_user'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }
}