<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;

class LoginGuruController extends Controller
{
    public function index()
    {
        return view('guru-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required'
        ]);

        $guru = Guru::where('nip', $request->nip)->first();

        if (!$guru) {
            return back()->with('error', 'NIP tidak ditemukan');
        }

        session([
            'login_guru' => true,
            'id_guru' => $guru->id_guru,
            'nama_guru' => $guru->nama,
            'nip' => $guru->nip
        ]);

        return redirect()->route('dashboard.guru');
    }

    public function dashboard()
    {
        if (!session('login_guru')) {
            return redirect()->route('guru.login');
        }

        return view('dashboard-guru');
    }

    public function logout()
    {
        session()->forget([
            'login_guru',
            'id_guru',
            'nama_guru',
            'nip'
        ]);

        return redirect()->route('dashboard');
    }
}