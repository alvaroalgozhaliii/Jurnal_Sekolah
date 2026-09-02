@extends('layouts.app')

@section('title', 'Data Kelas — Jurnal Sekolah')
@section('page-title', 'Data Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Kelas</h1>
        <p class="page-subtitle">Kelola Master Rombongan Belajar / Kelas Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.create') }}" class="btn btn-primary">+ Tambah Kelas</a>
        <a href="{{ route('kelas.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- CSV Import Card --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong class="text-navy" style="font-size:14px;">Import Data Kelas via CSV:</strong>
                <a href="{{ route('kelas.import-template') }}" class="btn btn-secondary btn-sm" style="font-size:12px; padding:4px 10px;">
                    Download Template CSV
                </a>
            </div>
            <form action="{{ route('kelas.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required style="font-size:12px;">
                <button type="submit" class="btn btn-primary btn-sm">Upload &amp; Import</button>
            </form>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('kelas.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama kelas, tingkat, jurusan, wali kelas..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($kelas->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama_kelas }}</td>
                        <td><span class="badge badge-navy">{{ $item->tingkat }}</span></td>
                        <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>{{ $item->wali_kelas ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('kelas.show', $item->id_kelas) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('kelas.edit', $item->id_kelas) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('kelas.destroy', $item->id_kelas) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kelas ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data kelas.</div>
        </div>
        @endif
    </div>
</div>
@endsection