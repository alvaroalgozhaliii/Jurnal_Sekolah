@extends('layouts.app')

@section('title', 'Detail Jadwal — Jurnal Sekolah')
@section('page-title', 'Detail Jadwal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Jadwal Pelajaran</h1>
        <p class="page-subtitle">Informasi Lengkap Sesi Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('jadwal.edit', $jadwal->id_jadwal) }}" class="btn btn-primary">Edit Data</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Jadwal</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>Hari</th><td class="fw-bold">{{ $jadwal->hari }}</td></tr>
                <tr><th>Jam Ke</th><td class="fw-bold">Jam ke-{{ $jadwal->jam_ke }}</td></tr>
                <tr><th>Waktu KBM</th><td>{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</td></tr>
                <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $jadwal->kelas->nama_kelas ?? '-' }}</span></td></tr>
                <tr><th>Guru Pengajar</th><td class="fw-bold text-navy">{{ $jadwal->guru->nama ?? '-' }}</td></tr>
                <tr><th>Mata Pelajaran</th><td class="fw-bold">{{ $jadwal->mapel }}</td></tr>
                <tr><th>Ruang Kelas</th><td>{{ $jadwal->ruang ?? '-' }}</td></tr>
                <tr><th>Status Jadwal</th><td>
                    @if($jadwal->aktif)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection