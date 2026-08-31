@extends('layouts.app')

@section('title', 'Detail Absensi Siswa — Jurnal Sekolah')
@section('page-title', 'Detail Absensi Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Absensi Siswa</h1>
        <p class="page-subtitle">Informasi status kehadiran siswa</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        @php
            $isFromOrtu = str_contains($absensi->keterangan ?? '', 'Orang Tua');
        @endphp
        @if(!Auth::user()->isSiswa() && !$isFromOrtu)
        <a href="{{ route('absensi-siswa.edit', $absensi->id_absensi) }}" class="btn btn-primary">Edit Data</a>
        @endif
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Detail Kehadiran</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @php
            $st = strtolower($absensi->status);
            $badgeCls = match($st) {
                'hadir' => 'badge-success',
                'izin' => 'badge-info',
                'sakit' => 'badge-purple',
                'alpa' => 'badge-danger',
                'terlambat' => 'badge-warning',
                default => 'badge-gray'
            };
        @endphp
        <table class="info-table">
            <tbody>
                <tr><th>Nama Siswa</th><td class="fw-bold text-navy">{{ $absensi->siswa->nama ?? '-' }}</td></tr>
                <tr><th>NIS</th><td class="fw-bold text-muted">{{ $absensi->siswa->nis ?? '-' }}</td></tr>
                <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $absensi->siswa->kelas->nama_kelas ?? '-' }}</span></td></tr>
                <tr><th>Tanggal Jurnal</th><td class="fw-bold">{{ $absensi->jurnal->tanggal ?? '-' }}</td></tr>
                <tr><th>Mata Pelajaran</th><td>{{ $absensi->jurnal->mapel ?? '-' }}</td></tr>
                <tr><th>Status Kehadiran</th><td><span class="badge {{ $badgeCls }}">{{ strtoupper($absensi->status) }}</span></td></tr>
                <tr><th>Keterangan</th><td>{{ $absensi->keterangan ?? '-' }}</td></tr>
                <tr><th>Dicatat Pada</th><td class="text-muted">{{ $absensi->created_at }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection