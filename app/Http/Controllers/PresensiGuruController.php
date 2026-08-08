<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PresensiMasuk;
use Carbon\Carbon;

class PresensiGuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();

        $presensiHariIni = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        $riwayatPresensi = PresensiMasuk::where('id_user', $user->id_user)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.presensi-saya', compact('presensiHariIni', 'riwayatPresensi'));
    }

    public function presensiMasuk(Request $request)
    {
        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        $existing = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        PresensiMasuk::create([
            'id_user' => $user->id_user,
            'tanggal' => $todayDate,
            'jam_masuk' => $currentTime,
            'keterangan' => $request->input('keterangan', 'Hadir tepat waktu'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Presensi masuk berhasil dicatat pada jam ' . $currentTime);
    }

    public function presensiKeluar(Request $request)
    {
        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        $presensi = PresensiMasuk::where('id_user', $user->id_user)
            ->where('tanggal', $todayDate)
            ->first();

        if (!$presensi) {
            return back()->with('error', 'Anda belum melakukan presensi masuk hari ini.');
        }

        if ($presensi->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan presensi keluar hari ini.');
        }

        $presensi->update([
            'jam_keluar' => $currentTime,
        ]);

        return back()->with('success', 'Presensi keluar berhasil dicatat pada jam ' . $currentTime);
    }
}
