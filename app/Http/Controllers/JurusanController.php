<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Jurusan::query();

        if ($search) {
            $query->where('nama_jurusan', 'like', "%{$search}%")
                  ->orWhere('rombel', 'like', "%{$search}%");
        }

        $jurusan = $query->orderBy('nama_jurusan', 'asc')->get();
        return view('jurusan.index', compact('jurusan', 'search'));
    }

    public function create()
    {
        return view('jurusan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:60|unique:jurusan,nama_jurusan',
            'rombel' => 'required|string|max:10|unique:jurusan,rombel',
            'maks_rombel' => 'required|numeric|min:1',
        ]);

        Jurusan::create([
            'nama_jurusan' => $request->nama_jurusan,
            'rombel' => $request->rombel,
            'maks_rombel' => $request->maks_rombel,
        ]);

        return redirect()->route('jurusan.index')->with('success', 'Data jurusan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $jurusan = Jurusan::with('kelas')->findOrFail($id);
        return view('jurusan.show', compact('jurusan'));
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'nama_jurusan' => 'required|string|max:60|unique:jurusan,nama_jurusan,' . $id . ',id_jurusan',
            'rombel' => 'required|string|max:10|unique:jurusan,rombel,' . $id . ',id_jurusan',
            'maks_rombel' => 'required|numeric|min:1',
        ]);

        $jurusan->update([
            'nama_jurusan' => $request->nama_jurusan,
            'rombel' => $request->rombel,
            'maks_rombel' => $request->maks_rombel,
        ]);

        return redirect()->route('jurusan.index')->with('success', 'Data jurusan berhasil diupdate.');
    }

    public function destroy($id)
    {
        Jurusan::findOrFail($id)->delete();
        return redirect()->route('jurusan.index')->with('success', 'Data jurusan berhasil dihapus.');
    }

    public function trash()
    {
        $jurusan = Jurusan::onlyTrashed()->get();
        return view('jurusan.trash', compact('jurusan'));
    }

    public function restore($id)
    {
        Jurusan::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('jurusan.trash')->with('success', 'Data jurusan berhasil direstore.');
    }

    public function forceDelete($id)
    {
        Jurusan::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('jurusan.trash')->with('success', 'Data jurusan berhasil dihapus permanen.');
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_data_jurusan.csv"',
        ];

        $sample = [
            ['nama_jurusan', 'rombel', 'maks_rombel'],
            ['Rekayasa Perangkat Lunak', 'RPL', 2],
            ['Teknik Komputer & Jaringan', 'TKJ', 2],
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
            $namaJurusan = $data['nama_jurusan'] ?? '';
            $rombel = $data['rombel'] ?? ($data['kode_rombel'] ?? ($data['singkatan'] ?? ''));
            $maksRombel = (int)($data['maks_rombel'] ?? 2);

            if (empty($namaJurusan) || empty($rombel)) {
                $skipped++;
                continue;
            }

            if (Jurusan::where('nama_jurusan', $namaJurusan)->orWhere('rombel', $rombel)->exists()) {
                $skipped++;
                continue;
            }

            Jurusan::create([
                'nama_jurusan' => $namaJurusan,
                'rombel' => $rombel,
                'maks_rombel' => $maksRombel > 0 ? $maksRombel : 2,
            ]);

            $inserted++;
        }

        return back()->with('success', "Import CSV berhasil! {$inserted} data jurusan ditambahkan, {$skipped} data dilewati.");
    }
}
