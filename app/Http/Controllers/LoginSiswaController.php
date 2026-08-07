<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;

class LoginSiswaController extends Controller
{
    public function index()
    {
        return view('siswa-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required'
        ]);

        $siswa = Siswa::where('nis', $request->nis)->first();

        if (!$siswa) {
            return back()->with('error', 'NIS tidak ditemukan');
        }

        session([
            'login_siswa' => true,
            'id_siswa' => $siswa->id_siswa,
            'nama_siswa' => $siswa->nama,
            'nis' => $siswa->nis
        ]);

        return redirect()->route('dashboard.siswa');
    }

    public function dashboard()
    {
        if (!session('login_siswa')) {
            return redirect()->route('siswa.login');
        }

        return view('dashboard-siswa');
    }

    public function logout()
    {
        session()->forget([
            'login_siswa',
            'id_siswa',
            'nama_siswa',
            'nis'
        ]);

        return redirect()->route('dashboard');
    }
}