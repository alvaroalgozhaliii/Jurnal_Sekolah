<?php

namespace App\Http\Controllers;

use App\Models\JadwalWaka;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WakaKurikulumController extends Controller
{
    public function index()
    {
        $jadwal = $this->jadwal();
        $wakas = $this->wakas();

        return view('waka-kurikulum.index', compact('jadwal', 'wakas'));
    }

    public function create()
    {
        return view('waka-kurikulum.create', ['wakas' => $this->wakas()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        JadwalWaka::create($data);

        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Waka berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwalWaka = JadwalWaka::with('waka')->findOrFail($id);

        return view('waka-kurikulum.show', compact('jadwalWaka'));
    }

    public function edit($id)
    {
        $jadwalWaka = JadwalWaka::findOrFail($id);

        return view('waka-kurikulum.edit', [
            'jadwalWaka' => $jadwalWaka,
            'wakas' => $this->wakas(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalWaka::findOrFail($id);
        $jadwal->update($this->validated($request, $id));

        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Waka berhasil diubah.');
    }

    public function destroy($id)
    {
        JadwalWaka::findOrFail($id)->delete();

        return redirect()->route('waka-kurikulum.index')->with('success', 'Jadwal Waka berhasil dihapus.');
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
                    ->whereIn('role', ['waka_sdm', 'waka_kesiswaan', 'waka_kurikulum'])
                    ->where('aktif', 1)),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function jadwal()
    {
        return JadwalWaka::with('waka')->orderBy('tanggal')->get();
    }

    private function wakas()
    {
        return User::whereIn('role', ['waka_sdm', 'waka_kesiswaan', 'waka_kurikulum'])
            ->where('aktif', 1)
            ->orderBy('nama')
            ->get();
    }
}