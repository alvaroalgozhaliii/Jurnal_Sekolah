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
        $query = Jadwal::with(['kelas', 'guru']);

        // Jika user adalah Guru, batasi hanya melihat jadwal miliknya sendiri
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

        return view('jadwal.index', compact('jadwal', 'search'));
    }

    public function create(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $guru = Guru::orderBy('nama', 'asc')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        $selectedKelasId = $request->get('id_kelas');

        return view('jadwal.create', compact('kelas', 'guru', 'mapel', 'selectedKelasId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|numeric',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'mapel' => 'required|string',
            'ruang' => 'nullable|string|max:20',
        ]);

        $hari = $request->hari;
        $jamKe = (int) $request->jam_ke;

        // 1. Validasi alokasi jam KBM
        $waktu = KbmService::getAlokasiWaktu($hari, $jamKe);
        if (!$waktu) {
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
            'aktif' => 1,
        ]);

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
            'jam_ke' => 'required|numeric',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'mapel' => 'required|string',
            'ruang' => 'nullable|string|max:20',
        ]);

        $hari = $request->hari;
        $jamKe = (int) $request->jam_ke;

        // 1. Validasi alokasi jam KBM
        $waktu = KbmService::getAlokasiWaktu($hari, $jamKe);
        if (!$waktu) {
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
}