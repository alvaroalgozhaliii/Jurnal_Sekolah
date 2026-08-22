@extends('layouts.app')

@section('title', 'Trash Guru — Jurnal Sekolah')
@section('page-title', 'Trash Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Guru</h1>
        <p class="page-subtitle">Data Guru yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Guru</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($guru->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Bidang Studi</th>
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
                        <td class="action-col">
                            <form action="{{ route('guru.restore', $item->id_guru) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore Data</button>
                            </form>
                            <form action="{{ route('guru.forceDelete', $item->id_guru) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada data guru di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
