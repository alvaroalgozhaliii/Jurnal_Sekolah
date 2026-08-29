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
}