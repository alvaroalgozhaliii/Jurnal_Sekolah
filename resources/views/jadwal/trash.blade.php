@extends('layouts.app')

@section('title', 'Trash Jadwal — Jurnal Sekolah')
@section('page-title', 'Trash Jadwal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trash - Data Jadwal</h1>
        <p class="page-subtitle">Data Jadwal Pelajaran yang Dihapus (Soft Delete)</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Jadwal</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($jadwal->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Hari / Jam</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $item->hari }} (Jam ke-{{ $item->jam_ke }})</td>
                        <td><span class="badge badge-navy">{{ $item->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td class="action-col">
                            <form action="{{ route('jadwal.restore', $item->id_jadwal) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('jadwal.forceDelete', $item->id_jadwal) }}" method="POST" style="display:inline;">
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
            <div class="empty-state-text">Tidak ada data jadwal di trash.</div>
        </div>
        @endif
    </div>
</div>
@endsection
