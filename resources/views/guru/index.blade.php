@extends('layouts.app')

@section('title', 'Data Guru — Jurnal Sekolah')
@section('page-title', 'Data Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle">Kelola Master Data Guru Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('guru.create') }}" class="btn btn-primary">+ Tambah Guru</a>
        <a href="{{ route('guru.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('guru.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama guru, NIP, bidang studi..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Bidang Studi</th>
                        <th>No Telepon</th>
                        <th>Akun User</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guru as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td class="text-muted">{{ $item->nip ?? '-' }}</td>
                        <td>{{ $item->bidang_studi ?? '-' }}</td>
                        <td>{{ $item->no_telp ?? '-' }}</td>
                        <td>
                            @if($item->user)
                                <span class="badge badge-success">{{ $item->user->username }}</span>
                            @else
                                <span class="badge badge-gray">Belum ada akun</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('guru.show', $item->id_guru) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('guru.edit', $item->id_guru) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('guru.destroy', $item->id_guru) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data guru ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection