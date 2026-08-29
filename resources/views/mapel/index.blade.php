@extends('layouts.app')

@section('title', 'Mata Pelajaran — Jurnal Sekolah')
@section('page-title', 'Mata Pelajaran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Mata Pelajaran</h1>
        <p class="page-subtitle">Kelola Master Mata Pelajaran Kurikulum Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.create') }}" class="btn btn-primary">+ Tambah Mapel</a>
        <a href="{{ route('mapel.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('mapel.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama mata pelajaran, kode mapel..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
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
                            <a href="{{ route('mapel.show', $item->id_mapel) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('mapel.edit', $item->id_mapel) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('mapel.destroy', $item->id_mapel) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus mata pelajaran ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data mata pelajaran.</div>
        </div>
        @endif
    </div>
</div>
@endsection
