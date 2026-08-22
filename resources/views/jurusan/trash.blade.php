@extends('layouts.app')

@section('title', 'Trash Jurusan — Jurnal Sekolah')
@section('page-title', 'Trash Jurusan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Jurusan</h1>
        <p class="page-subtitle">Data Jurusan yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Jurusan</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($jurusan->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kode Jurusan</th>
                        <th>Nama Jurusan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusan as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $item->kode_jurusan ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama_jurusan }}</td>
                        <td class="action-col">
                            <form action="{{ route('jurusan.restore', $item->id_jurusan) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('jurusan.forceDelete', $item->id_jurusan) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada data jurusan di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
