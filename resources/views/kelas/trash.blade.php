@extends('layouts.app')

@section('title', 'Trash Kelas — Jurnal Sekolah')
@section('page-title', 'Trash Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Kelas</h1>
        <p class="page-subtitle">Data Kelas yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Kelas</a>
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
                        <td class="action-col">
                            <form action="{{ route('kelas.restore', $item->id_kelas) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('kelas.forceDelete', $item->id_kelas) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada data kelas di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
