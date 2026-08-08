<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiGuruPiket;
use App\Models\Jadwal;
use App\Models\PresensiMasuk;
use App\Models\User;
use Carbon\Carbon;

class PresensiPiketController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $selectedDate = Carbon::parse($tanggal);
        $dayIndo = $days[$selectedDate->format('l')] ?? 'Senin';

        $jadwalHariIni = Jadwal::with(['guru', 'kelas'])
            ->where('hari', $dayIndo)
            ->where('aktif', 1)
            ->get();

        $absensiPiketList = AbsensiGuruPiket::where('tanggal', $tanggal)
            ->get()
            ->keyBy('id_jadwal');

        $presensiGuruMasuk = PresensiMasuk::where('tanggal', $tanggal)
            ->get()
            ->keyBy('id_user');

        return view('piket.presensi-index', compact(
            'jadwalHariIni',
            'absensiPiketList',
            'presensiGuruMasuk',
            'tanggal'
        ));
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'tanggal' => 'required|date',
            'status_guru' => 'required|in:hadir,tidak_hadir,terlambat,digantikan',
        ]);

        AbsensiGuruPiket::updateOrCreate(
            [
                'id_jadwal' => $request->id_jadwal,
                'tanggal' => $request->tanggal,
            ],
            [
                'status_guru' => $request->status_guru,
                'pengganti' => $request->pengganti,
                'keterangan' => $request->keterangan,
                'dicatat_oleh' => auth()->id(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Status kehadiran guru piket berhasil disimpan.');
    }
}
