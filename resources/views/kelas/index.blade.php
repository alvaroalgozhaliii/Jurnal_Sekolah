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