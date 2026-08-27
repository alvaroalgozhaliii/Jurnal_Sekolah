<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JurnalHarianController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $query = JurnalHarian::with(['guru', 'jadwal.kelas']);

        // Role filtering
        if ($user->isGuru()) {
            $guru = $user->guru;
            $query->where('id_guru', $guru ? $guru->id_guru : 0);
        } else {
            // Admin & Piket can filter
            if ($request->filled('id_guru')) {
                $query->where('id_guru', $request->id_guru);
            }
            if ($request->filled('id_kelas')) {
                $kelasId = $request->id_kelas;
                $query->whereHas('jadwal', function ($q) use ($kelasId) {
                    $q->where('id_kelas', $kelasId);
                });
            }
            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mapel', 'like', "%{$search}%")
                  ->orWhere('materi', 'like', "%{$search}%")
                  ->orWhere('sub_materi', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%")
                  ->orWhereHas('guru', function($qg) use ($search) {
                      $qg->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jadwal.kelas', function($qk) use ($search) {
                      $qk->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $jurnal_harian = $query->orderBy('tanggal', 'desc')->get();
        $guruList = Guru::all();
        $kelasList = Kelas::all();

        return view('jurnal_harian.index', compact('jurnal_harian', 'guruList', 'kelasList', 'search'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->isGuru()) {
            $guru = $user->guru;
            if (!$guru) {
                return back()->with('error', 'Profil guru Anda tidak ditemukan.');
            }
            $jadwalList = Jadwal::with('kelas')
                ->where('id_guru', $guru->id_guru)
                ->where('aktif', 1)
                ->get();
        } else {
            $jadwalList = Jadwal::with(['kelas', 'guru'])->where('aktif', 1)->get();
        }

        return view('jurnal_harian.create', compact('jadwalList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'tanggal' => 'required|date',
            'materi' => 'required|string',
        ]);

        $jadwal = Jadwal::findOrFail($request->id_jadwal);
        $user = Auth::user();

        // Enforce time limit for Guru
        if ($user->isGuru()) {
            $checkTime = $this->isJournalTimeExpired($jadwal, $request->tanggal);
            if ($checkTime['expired']) {
                return back()->with('error', $checkTime['message']);
            }
        }

        $idGuru = $user->isGuru() ? ($user->guru->id_guru ?? $jadwal->id_guru) : $jadwal->id_guru;

        JurnalHarian::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'tanggal' => $request->tanggal,
            'id_guru' => $idGuru,
            'mapel' => $jadwal->mapel,
            'materi' => $request->materi,
            'sub_materi' => $request->sub_materi,
            'catatan_pengajaran' => $request->catatan_pengajaran,
            'status_keterlaksanaan' => $request->input('status_keterlaksanaan', 'terlaksana'),
            'created_by' => $user->id_user,
        ]);

        return redirect()->route('jurnal-harian.index')->with('success', 'Jurnal harian berhasil disimpan.');
    }

    public function show($id)
    {
        $jurnal_harian = JurnalHarian::with(['guru', 'jadwal.kelas', 'absensiSiswa.siswa'])->findOrFail($id);
        return view('jurnal_harian.show', compact('jurnal_harian'));
    }

    public function edit($id)
    {
        $jurnal_harian = JurnalHarian::with('jadwal.kelas')->findOrFail($id);
        $user = Auth::user();

        if ($user->isGuru() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
            abort(403, 'Anda hanya dapat mengedit jurnal milik Anda sendiri.');
        }

        if ($user->isGuru()) {
            $checkTime = $this->isJournalTimeExpired($jurnal_harian->jadwal, $jurnal_harian->tanggal);
            if ($checkTime['expired']) {
                return redirect()->route('jurnal-harian.index')->with('error', $checkTime['message']);
            }
        }

        return view('jurnal_harian.edit', compact('jurnal_harian'));
    }

    public function update(Request $request, $id)
    {
        $jurnal_harian = JurnalHarian::with('jadwal')->findOrFail($id);
        $user = Auth::user();

        if ($user->isGuru() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
            abort(403, 'Anda hanya dapat mengedit jurnal milik Anda sendiri.');
        }

        if ($user->isGuru()) {
            $checkTime = $this->isJournalTimeExpired($jurnal_harian->jadwal, $jurnal_harian->tanggal);
            if ($checkTime['expired']) {
                return back()->with('error', $checkTime['message']);
            }
        }

        $request->validate([
            'materi' => 'required|string',
        ]);

        $jurnal_harian->update([
            'materi' => $request->materi,
            'sub_materi' => $request->sub_materi,
            'catatan_pengajaran' => $request->catatan_pengajaran,
            'status_keterlaksanaan' => $request->input('status_keterlaksanaan', $jurnal_harian->status_keterlaksanaan),
        ]);

        return redirect()->route('jurnal-harian.index')->with('success', 'Jurnal harian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurnal_harian = JurnalHarian::findOrFail($id);
        $user = Auth::user();

        if ($user->isGuru() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
            abort(403, 'Anda hanya dapat menghapus jurnal milik Anda sendiri.');
        }

        $jurnal_harian->delete();
        return redirect()->route('jurnal-harian.index')->with('success', 'Jurnal harian dipindahkan ke trash.');
    }

    public function trash()
    {
        $jurnal_harian = JurnalHarian::onlyTrashed()->with(['guru', 'jadwal.kelas'])->get();
        return view('jurnal_harian.trash', compact('jurnal_harian'));
    }

    public function restore($id)
    {
        JurnalHarian::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('jurnal-harian.trash')->with('success', 'Jurnal harian berhasil dikembalikan.');
    }

    public function forceDelete($id)
    {
        JurnalHarian::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('jurnal-harian.trash')->with('success', 'Jurnal harian berhasil dihapus permanen.');
    }

    private function isJournalTimeExpired($jadwal, $tanggal)
    {
        if (!$jadwal || !$jadwal->waktu_selesai) {
            return ['expired' => false];
        }

        $batasMenit = (int) Pengaturan::getVal('batas_waktu_jurnal_menit', 15, 'admin');
        
        $journalDate = Carbon::parse($tanggal)->toDateString();
        $today = Carbon::today()->toDateString();

        // If journal is for a past day, it's expired for Guru
        if ($journalDate < $today) {
            return [
                'expired' => true,
                'message' => 'Waktu pengisian jurnal untuk tanggal ' . $tanggal . ' telah berakhir.'
            ];
        }

        // If journal is for today, check end time + grace period
        if ($journalDate === $today) {
            $endTimeWithGrace = Carbon::parse($today . ' ' . $jadwal->waktu_selesai)->addMinutes($batasMenit);
            if (Carbon::now()->greaterThan($endTimeWithGrace)) {
                return [
                    'expired' => true,
                    'message' => "Waktu pengisian jurnal telah berakhir pada " . $endTimeWithGrace->format('H:i') . " (Batas toleransi: {$batasMenit} menit setelah jam pelajaran berakhir)."
                ];
            }
        }

        return ['expired' => false];
    }
}