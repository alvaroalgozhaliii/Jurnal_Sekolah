@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas — Jurnal Sekolah')
@section('page-title', 'Dashboard Wali Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Wali Kelas</h1>
        <p class="page-subtitle">Monitoring Kelas & Presensi Siswa Bimbingan</p>
    </div>
</div>

@if($kelas)
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title"> Kelas Bimbingan: {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Wali Kelas:</span> <strong>{{ $kelas->wali_kelas ?? Auth::user()->nama }}</strong></div>
            <div><span class="text-muted">Jurusan:</span> <strong>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</strong></div>
            <div><span class="text-muted">Total Siswa:</span> <strong><span class="badge badge-navy">{{ $totalSiswa }} Siswa</span></strong></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> Presensi Siswa Kelas Hari Ini</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($presensiHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead><tr><th>NISN</th><th>Nama Siswa</th><th>Status</th><th>Jam Masuk</th><th>Keterangan</th></tr></thead>
                <tbody>
                @foreach($presensiHariIni as $p)
                @php
                    $st = strtolower($p->status);
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
                    <td class="text-muted">{{ $p->siswa->NISN ?? '-' }}</td>
                    <td class="fw-bold text-navy">{{ $p->siswa->nama ?? '-' }}</td>
                    <td><span class="badge {{ $badgeCls }}">{{ strtoupper($p->status) }}</span></td>
                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon"></div>
            <div class="empty-state-text">Belum ada presensi yang dicatat untuk siswa kelas ini hari ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    
    <div>Anda belum ditugaskan sebagai Wali Kelas pada kelas tertentu.</div>
</div>
@endif
@endsection
