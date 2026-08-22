@extends('layouts.app')

@section('title', 'Absensi Siswa — Jurnal Sekolah')
@section('page-title', 'Absensi Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Absensi Siswa</h1>
        <p class="page-subtitle">Rekapitulasi Kehadiran Siswa dalam KBM</p>
    </div>
    @if(!Auth::user()->isSiswa())
    <div class="page-actions">
        <a href="{{ route('absensi-siswa.create') }}" class="btn btn-primary">+ Input Absensi Siswa</a>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal Jurnal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $item)
                    @php
                        $st = strtolower($item->status);
                        $badgeCls = match($st) {
                            'hadir' => 'badge-success',
                            'izin' => 'badge-info',
                            'sakit' => 'badge-purple',
                            'alpa' => 'badge-danger',
                            'terlambat' => 'badge-warning',
                            default => 'badge-gray'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $item->jurnal->tanggal ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $item->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $item->jurnal->mapel ?? '-' }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper($item->status) }}</span></td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('absensi-siswa.show', $item->id_absensi) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if(!Auth::user()->isSiswa())
                            <a href="{{ route('absensi-siswa.edit', $item->id_absensi) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('absensi-siswa.destroy', $item->id_absensi) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data absensi?')">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-text">Tidak ada data absensi siswa.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection