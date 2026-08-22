@extends('layouts.app')

@section('title', 'Trash Siswa — Jurnal Sekolah')
@section('page-title', 'Trash Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Siswa</h1>
        <p class="page-subtitle">Data Siswa yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Siswa</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($siswa->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $item->nis }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td><span class="badge badge-navy">{{ $item->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="action-col">
                            <form action="{{ route('siswa.restore', $item->id_siswa) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore Data</button>
                            </form>
                            <form action="{{ route('siswa.forceDelete', $item->id_siswa) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada data siswa di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
