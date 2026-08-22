@extends('layouts.app')

@section('title', 'Trash Mapel — Jurnal Sekolah')
@section('page-title', 'Trash Mapel')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Mata Pelajaran</h1>
        <p class="page-subtitle">Data Mapel yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Mapel</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($mapel->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mapel as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $item->kode_mapel ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama_mapel }}</td>
                        <td class="action-col">
                            <form action="{{ route('mapel.restore', $item->id_mapel) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('mapel.forceDelete', $item->id_mapel) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data mapel di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
