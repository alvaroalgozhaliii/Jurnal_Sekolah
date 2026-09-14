<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PresensiMasuk;
use Carbon\Carbon;

use App\Services\KbmService;

class PresensiGuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();
        $hariIndo = match (Carbon::today()->format('l')) {
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            default => 'Senin'
        };

        $presensiHariIni = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        $riwayatPresensi = PresensiMasuk::where('id_user', $user->id_user)
            ->orderBy('tanggal', 'desc')
            ->get();

        $jamMasuk = KbmService::getJamMasuk();
        $jamPulang = KbmService::getJamPulang($hariIndo);
        $toleransiTerlambat = KbmService::getToleransiTerlambat();

        return view('guru.presensi-saya', compact(
            'presensiHariIni',
            'riwayatPresensi',
            'jamMasuk',
            'jamPulang',
            'toleransiTerlambat'
        ));
    }

    public function presensiMasuk(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $timeShort = $now->format('H:i');

        $existing = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        $jamMasuk = KbmService::getJamMasuk();
        $toleransi = KbmService::getToleransiTerlambat();

        // Hitung batas waktu toleransi
        $jamMasukCarbon = Carbon::createFromFormat('H:i', $jamMasuk, config('app.timezone', 'Asia/Jakarta'));
        $batasToleransiCarbon = $jamMasukCarbon->copy()->addMinutes($toleransi);
        $batasToleransi = $batasToleransiCarbon->format('H:i');

        $keteranganInput = trim($request->input('keterangan', ''));
        if ($timeShort > $batasToleransi) {
            $menitTerlambat = $jamMasukCarbon->diffInMinutes($now, false);
            $defaultKet = 'Terlambat ' . ($menitTerlambat > 0 ? $menitTerlambat : 1) . ' menit';
            $keterangan = $keteranganInput ? "{$keteranganInput} ({$defaultKet})" : $defaultKet;
        } else {
            $keterangan = $keteranganInput ?: 'Hadir tepat waktu';
        }

        PresensiMasuk::create([
            'id_user' => $user->id_user,
            'tanggal' => $todayDate,
            'jam_masuk' => $currentTime,
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Presensi masuk berhasil dicatat pada jam ' . $currentTime);
    }

    public function presensiKeluar(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $timeShort = $now->format('H:i');

        $hariIndo = match ($now->format('l')) {
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            default => 'Senin'
        };

        $presensi = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        if (!$presensi) {
            return back()->with('error', 'Anda belum melakukan presensi masuk hari ini.');
        }

        if ($presensi->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan presensi keluar hari ini.');
        }

        $jamPulang = KbmService::getJamPulang($hariIndo);
        $presensi->update([
            'jam_keluar' => $currentTime,
        ]);

        $pesan = 'Presensi keluar berhasil dicatat pada jam ' . $currentTime;
        if ($timeShort < $jamPulang) {
            $pesan .= " (Presensi sebelum jam kepulangan resmi sekolah: {$jamPulang} WIB).";
        }

        return back()->with('success', $pesan);
    }
}
