@extends('layouts.app')

@section('title', 'Jurnal Kelas Bimbingan — Jurnal Sekolah')
@section('page-title', 'Jurnal Kelas Bimbingan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jurnal Harian Kelas Bimbingan</h1>
        <p class="page-subtitle">Monitoring pelaksanaan pengajaran KBM di kelas bimbingan</p>
    </div>
</div>

@if($kelas)
<div class="card mb-24">
    <div class="card-body">
        <div>Kelas Bimbingan: <strong><span class="badge badge-navy" style="font-size:14px;">{{ $kelas->nama_kelas }}</span></strong></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Jurnal Harian Kelas {{ $kelas->nama_kelas }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jurnalList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari / Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Materi</th>
                        <th>Status Keterlaksanaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnalList as $j)
                    @php
                        $st = strtolower($j->status_keterlaksanaan);
                        $badgeCls = match($st) {
                            'terlaksana' => 'badge-success',
                            'tidak_terlaksana', 'kosong' => 'badge-danger',
                            'pengganti' => 'badge-amber',
                            default => 'badge-gray'
                        };
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $j->tanggal }}</td>
                        <td>{{ $j->jadwal->hari ?? '-' }} / Jam {{ $j->jadwal->jam_ke ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? '-' }}</td>
                        <td>{{ $j->materi }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $j->status_keterlaksanaan)) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada jurnal harian yang tercatat di kelas ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Anda belum ditugaskan sebagai Wali Kelas.</div>
</div>
@endif
@endsection
