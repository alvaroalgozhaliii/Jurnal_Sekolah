@extends('layouts.app')

@section('title', 'Trash Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Trash Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Jurnal Harian</h1>
        <p class="page-subtitle">Data jurnal harian yang telah dihapus (soft delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali ke Jurnal Harian</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($jurnal_harian->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal</th>
                        <th>Guru Pengajar</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnal_harian as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $item->tanggal }}</td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                        <td class="action-col">
                            <form action="{{ route('jurnal-harian.restore', $item->id_jurnal) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('jurnal-harian.forceDelete', $item->id_jurnal) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada jurnal di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection