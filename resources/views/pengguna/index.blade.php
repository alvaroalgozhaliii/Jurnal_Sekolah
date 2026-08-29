@extends('layouts.app')

@section('title', 'Manajemen Pengguna — Jurnal Sekolah')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Akun Pengguna</h1>
        <p class="page-subtitle">Kelola Seluruh Akun Login Pengguna System & Peran (Role)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengguna.create') }}" class="btn btn-primary">+ Tambah Pengguna Baru</a>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, username, role..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('pengguna.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($users->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role / Peran</th>
                        <th>Status Profil Terhubung</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td><span class="badge badge-navy">{{ $item->username }}</span></td>
                        <td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $item->role)) }}</span></td>
                        <td>
                            @if($item->guru)
                                <span class="badge badge-info">Guru: {{ $item->guru->nama }}</span>
                            @elseif($item->siswa)
                                <span class="badge badge-success">Siswa: {{ $item->siswa->nama }}</span>
                            @else
                                <span class="badge badge-gray">Akun Standalone</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengguna.show', $item->id_user) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('pengguna.edit', $item->id_user) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('pengguna.destroy', $item->id_user) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus akun pengguna ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data pengguna.</div>
        </div>
        @endif
    </div>
</div>
@endsection
