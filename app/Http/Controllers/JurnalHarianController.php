<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Pengaturan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JurnalHarianController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JurnalHarian::with(['guru', 'jadwal.kelas']);

        if ($user->isGuru() && !$user->isAdmin()) {
            $guru = $user->guru;
            $query->where('id_guru', $guru ? $guru->id_guru : 0);
        } else {
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

        $jurnal_harian = $query->orderBy('tanggal', 'desc')->get();
        $guruList = Guru::all();
        $kelasList = Kelas::all();
        $tanggal = $request->input('tanggal');
        $id_guru = $request->input('id_guru');
        $id_kelas = $request->input('id_kelas');

        return view('jurnal_harian.index', compact('jurnal_harian', 'guruList', 'kelasList', 'tanggal', 'id_guru', 'id_kelas'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $todayDate = $now->toDateString();

        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $days[$now->format('l')] ?? 'Senin';

        if ($user->isGuru() && !$user->isAdmin()) {
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

        // Attach active status for schedule filling eligibility
        foreach ($jadwalList as $j) {
            $j->is_active_now = $this->isScheduleActiveNow($j, $currentDayIndo, $now);
            $j->active_message = $this->getScheduleTimeMessage($j, $currentDayIndo, $now);
        }

        $jadwalSelected = null;
        if ($request->filled('id_jadwal')) {
            $jadwalSelected = $jadwalList->firstWhere('id_jadwal', (int) $request->id_jadwal);
        }

        return view('jurnal_harian.create', compact('jadwalList', 'jadwalSelected'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'materi' => 'required|string',
        ]);

        $jadwal = Jadwal::findOrFail($request->id_jadwal);
        $user = Auth::user();
        $now = Carbon::now();

        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayIndo = $days[$now->format('l')] ?? 'Senin';

        // Enforce teaching schedule time restriction for Guru
        if ($user->isGuru() && !$user->isAdmin()) {
            if (!$this->isScheduleActiveNow($jadwal, $currentDayIndo, $now, $request->tanggal)) {
                $msg = $this->getScheduleTimeMessage($jadwal, $currentDayIndo, $now, $request->tanggal);
                return back()->with('error', 'Tidak dapat mengisi jurnal: ' . $msg);
            }
        }

        $idGuru = $user->isGuru() ? ($user->guru->id_guru ?? $jadwal->id_guru) : $jadwal->id_guru;

        $jurnal = JurnalHarian::create([
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

        return redirect()->route('absensi-siswa.create', ['id_jurnal' => $jurnal->id_jurnal])->with('success', 'Jurnal harian disimpan. Silakan lanjutkan mengisi absensi siswa.');
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

        if ($user->isGuru() && !$user->isAdmin() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
            abort(403, 'Anda hanya dapat mengedit jurnal milik Anda sendiri.');
        }

        return view('jurnal_harian.edit', compact('jurnal_harian'));
    }

    public function update(Request $request, $id)
    {
        $jurnal_harian = JurnalHarian::with('jadwal')->findOrFail($id);
        $user = Auth::user();

        if ($user->isGuru() && !$user->isAdmin() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
            abort(403, 'Anda hanya dapat mengedit jurnal milik Anda sendiri.');
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

        if ($user->isGuru() && !$user->isAdmin() && $jurnal_harian->id_guru !== $user->guru?->id_guru) {
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

    private function isScheduleActiveNow($jadwal, $currentDayIndo, $now, $inputTanggal = null)
    {
        $targetDate = $inputTanggal ?? $now->toDateString();
        $todayDate = $now->toDateString();

        // If for future date, not active yet
        if ($targetDate > $todayDate) return false;

        // If for past date, check grace period setting (e.g. 15-60 mins)
        $batasMenit = (int) Pengaturan::getVal('batas_waktu_jurnal_menit', 60, 'admin');

        if ($targetDate === $todayDate) {
            if ($jadwal->hari !== $currentDayIndo) return false;

            if ($jadwal->waktu_mulai && $jadwal->waktu_selesai) {
                $startTime = Carbon::parse($todayDate . ' ' . $jadwal->waktu_mulai);
                $endTimeWithGrace = Carbon::parse($todayDate . ' ' . $jadwal->waktu_selesai)->addMinutes($batasMenit);

                return $now->greaterThanOrEqualTo($startTime) && $now->lessThanOrEqualTo($endTimeWithGrace);
            }
        }

        return true;
    }

    private function getScheduleTimeMessage($jadwal, $currentDayIndo, $now, $inputTanggal = null)
    {
        $targetDate = $inputTanggal ?? $now->toDateString();
        $todayDate = $now->toDateString();

        if ($targetDate === $todayDate) {
            if ($jadwal->hari !== $currentDayIndo) {
                return 'Hari ini bukan jadwal mengajar (' . $jadwal->hari . ').';
            }
            if ($jadwal->waktu_mulai && $jadwal->waktu_selesai) {
                $startTime = Carbon::parse($todayDate . ' ' . $jadwal->waktu_mulai);
                if ($now->lessThan($startTime)) {
                    return 'Belum waktunya (Waktu mengajar: ' . $jadwal->waktu_mulai . ' - ' . $jadwal->waktu_selesai . ').';
                }
                $batasMenit = (int) Pengaturan::getVal('batas_waktu_jurnal_menit', 60, 'admin');
                $endTimeWithGrace = Carbon::parse($todayDate . ' ' . $jadwal->waktu_selesai)->addMinutes($batasMenit);
                if ($now->greaterThan($endTimeWithGrace)) {
                    return 'Waktu pengisian jurnal telah berakhir (Batas: ' . $endTimeWithGrace->format('H:i') . ').';
                }
            }
        }

        return 'Waktu mengajar aktif.';
    }
}