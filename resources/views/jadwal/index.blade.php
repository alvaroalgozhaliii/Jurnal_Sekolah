@extends('layouts.app')

@section('title', 'Jadwal Pelajaran — Jurnal Sekolah')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Jadwal Pelajaran</h1>
        <p class="page-subtitle">Kelola Master Jadwal Mengajar KBM Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.create') }}" class="btn btn-primary">+ Tambah Jadwal</a>
        <a href="{{ route('jadwal.trash') }}" class="btn btn-secondary">Lihat Trash</a>
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
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Waktu</th>
                        <th>Kelas</th>
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Ruang</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $item)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $item->hari }}</td>
                        <td>Jam ke-{{ $item->jam_ke }}</td>
                        <td>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>
                        <td><span class="badge badge-navy">{{ $item->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $item->guru->nama ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                        <td>{{ $item->ruang ?? '-' }}</td>
                        <td>
                            @if($item->aktif)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('jadwal.show', $item->id_jadwal) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('jadwal.edit', $item->id_jadwal) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data jadwal pelajaran.</div>
        </div>
        @endif
    </div>
</div>
@endsection