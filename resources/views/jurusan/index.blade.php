@extends('layouts.app')

@section('title', 'Data Jurusan — Jurnal Sekolah')
@section('page-title', 'Data Jurusan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Jurusan</h1>
        <p class="page-subtitle">Kelola Master Program Keahlian / Jurusan Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurusan.create') }}" class="btn btn-primary">+ Tambah Jurusan</a>
        <a href="{{ route('jurusan.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('jurusan.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama jurusan, rombel..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('jurusan.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
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
                        <th>Kode</th>
                        <th>Nama Jurusan</th>
                        <th>Maks Rombel</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusan as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $item->rombel ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama_jurusan }}</td>
                        <td>{{ $item->maks_rombel ?? '-' }} kelas</td>
                        <td class="action-col">
                            <a href="{{ route('jurusan.show', $item->id_jurusan) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('jurusan.edit', $item->id_jurusan) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('jurusan.destroy', $item->id_jurusan) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jurusan ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data jurusan.</div>
        </div>
        @endif
    </div>
</div>
@endsection
