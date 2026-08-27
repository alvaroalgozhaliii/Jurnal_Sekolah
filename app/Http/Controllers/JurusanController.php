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
}
