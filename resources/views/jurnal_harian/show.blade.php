@extends('layouts.app')

@section('title', 'Detail Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Detail Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Jurnal Harian</h1>
        <p class="page-subtitle">Informasi Pelaksanaan KBM & Rekap Absensi Siswa</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card mb-24" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">Detail Pelaksanaan Pengajaran</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @php
            $st = strtolower($jurnal_harian->status_keterlaksanaan);
            $badgeCls = match($st) {
                'terlaksana' => 'badge-success',
                'tidak_terlaksana', 'kosong' => 'badge-danger',
                'pengganti' => 'badge-amber',
                default => 'badge-gray'
            };
        @endphp
        <table class="info-table">
            <tbody>
                <tr><th>Tanggal</th><td class="fw-bold">{{ $jurnal_harian->tanggal }}</td></tr>
                <tr><th>Guru Pengajar</th><td class="fw-bold text-navy">{{ $jurnal_harian->guru->nama ?? '-' }}</td></tr>
                <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $jurnal_harian->jadwal->kelas->nama_kelas ?? '-' }}</span></td></tr>
                <tr><th>Mata Pelajaran</th><td class="fw-bold">{{ $jurnal_harian->mapel }}</td></tr>
                <tr><th>Hari / Jam Ke</th><td>{{ $jurnal_harian->jadwal->hari ?? '-' }} / Jam ke-{{ $jurnal_harian->jadwal->jam_ke ?? '-' }}</td></tr>
                <tr><th>Waktu KBM</th><td>{{ $jurnal_harian->jadwal->waktu_mulai ?? '-' }} - {{ $jurnal_harian->jadwal->waktu_selesai ?? '-' }}</td></tr>
                <tr><th>Materi Utama</th><td class="fw-bold text-navy">{{ $jurnal_harian->materi }}</td></tr>
                <tr><th>Sub Materi</th><td>{{ $jurnal_harian->sub_materi ?? '-' }}</td></tr>
                <tr><th>Catatan Pengajaran</th><td>{{ $jurnal_harian->catatan_pengajaran ?? '-' }}</td></tr>
                <tr><th>Status Keterlaksanaan</th><td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $jurnal_harian->status_keterlaksanaan)) }}</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection