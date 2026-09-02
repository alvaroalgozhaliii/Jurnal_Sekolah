<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Guru::with('user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('bidang_studi', 'like', "%{$search}%");
            });
        }

        $guru = $query->orderBy('nama', 'asc')->get();
        return view('guru.index', compact('guru', 'search'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:30|unique:guru,nip',
            'bidang_studi' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if ($request->filled('username')) {
            $user = User::create([
                'nama' => $request->nama,
                'nip' => $request->nip,
                'username' => $request->username,
                'password' => Hash::make($request->password ?? 'guru123'),
                'role' => 'guru',
                'aktif' => 1,
                'created_at' => now(),
            ]);
            $userId = $user->id_user;
        }

        Guru::create([
            'id_user' => $userId,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
            'created_at' => now(),
        ]);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan');
    }

    public function show($id)
    {
        $guru = Guru::with(['user', 'jadwal.kelas', 'jurnalHarian'])->findOrFail($id);
        return view('guru.show', compact('guru'));
    }

    public function edit($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:30|unique:guru,nip,' . $id . ',id_guru',
            'bidang_studi' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'bidang_studi' => $request->bidang_studi,
            'no_telp' => $request->no_telp,
        ]);

        if ($guru->user) {
            $guru->user->update([
                'nama' => $request->nama,
                'nip' => $request->nip,
            ]);
        }

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diupdate');
    }

    public function destroy($id)
    {
        Guru::findOrFail($id)->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru dipindahkan ke trash');
    }

    public function trash()
    {
        $guru = Guru::onlyTrashed()->get();
        return view('guru.trash', compact('guru'));
    }

    public function restore($id)
    {
        Guru::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('guru.trash')->with('success', 'Data guru berhasil direstore');
    }

    public function forceDelete($id)
    {
        Guru::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('guru.trash')->with('success', 'Data guru dihapus permanen');
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_guru.csv"',
        ];

        $sample = [
            ['nama', 'nip', 'bidang_studi', 'no_telp', 'username', 'password'],
            ['Budi Santoso, S.Pd', '19800101 200312 1 001', 'Matematika', '081234567890', 'budi.santoso', 'guru123'],
            ['Siti Aminah, M.Pd', '19850202 200801 2 002', 'Bahasa Indonesia', '081234567891', 'siti.aminah', 'guru123'],
        ];

        return response()->stream(function() use ($sample) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($sample as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ]);

        try {
            $parsed = \App\Services\CsvImportService::parseCsv($request->file('csv_file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses file CSV: ' . $e->getMessage());
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($parsed['rows'] as $data) {
            $nama = $data['nama'] ?? '';
            if (empty($nama)) {
                $skipped++;
                continue;
            }

            $nip = !empty($data['nip']) ? $data['nip'] : null;
            if ($nip && Guru::where('nip', $nip)->exists()) {
                $skipped++;
                continue;
            }

            $bidangStudi = $data['bidang_studi'] ?? null;
            $noTelp = $data['no_telp'] ?? null;
            $username = $data['username'] ?? null;
            $password = !empty($data['password']) ? $data['password'] : 'guru123';

            $userId = null;
            if (!empty($username)) {
                if (User::where('username', $username)->exists()) {
                    $counter = 1;
                    $orig = $username;
                    while (User::where('username', $username)->exists()) {
                        $username = $orig . $counter;
                        $counter++;
                    }
                }

                $user = User::create([
                    'nama' => $nama,
                    'nip' => $nip,
                    'username' => $username,
                    'password' => Hash::make($password),
                    'role' => 'guru',
                    'aktif' => 1,
                ]);
                $userId = $user->id_user;
            }

            Guru::create([
                'id_user' => $userId,
                'nama' => $nama,
                'nip' => $nip,
                'bidang_studi' => $bidangStudi,
                'no_telp' => $noTelp,
            ]);

            $inserted++;
        }

        return back()->with('success', "Import CSV berhasil! {$inserted} data guru ditambahkan, {$skipped} data dilewati.");
    }
}