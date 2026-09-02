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
            'nisn' => 'required'
        ]);

        $siswa = Siswa::where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return back()->with('error', 'NISN tidak ditemukan');
        }

        session([
            'login_siswa' => true,
            'id_siswa' => $siswa->id_siswa,
            'nama_siswa' => $siswa->nama,
            'nisn' => $siswa->nisn
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
            'nisn'
        ]);

        return redirect()->route('dashboard');
    }
}