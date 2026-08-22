<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanIzin;

class KepalaSekolahController extends Controller
{
    public function index()
    {
        $pengajuanPending = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju'])
            ->where('status', 'pending_kepala')
            ->get();

        $pengajuanRiwayat = PengajuanIzin::with(['siswa.kelas', 'guru', 'pengaju'])
            ->where('status', '!=', 'pending_kepala')
            ->whereNotNull('id_kepala_approver')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kepala.dashboard', compact('pengajuanPending', 'pengajuanRiwayat'));
    }
}
