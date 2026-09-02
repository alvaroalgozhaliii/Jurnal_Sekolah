@extends('layouts.app')

@section('title', 'Dashboard Siswa — Jurnal Sekolah')
@section('page-title', 'Dashboard Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Siswa</h1>
        <p class="page-subtitle">Informasi Pelajaran & Presensi Saya</p>
    </div>
</div>

@if($siswa)
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title"> Profil Siswa: {{ $siswa->nama }}</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">NISN:</span> <strong>{{ $siswa->NISN }}</strong></div>
            <div><span class="text-muted">Kelas:</span> <strong>{{ $siswa->kelas->nama_kelas ?? '-' }}</strong></div>
            <div><span class="text-muted">Jurusan:</span> <strong>{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</strong></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"> Jadwal Pelajaran Hari Ini</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($jadwalHariIni->count() > 0)
                <div class="table-wrapper" style="border:none; border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Waktu</th>
                                <th>Mapel</th>
                                <th>Guru</th>
                                <th>Ruang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni as $j)
                                <tr>
                                    <td class="fw-bold">{{ $j->jam_ke }}</td>
                                    <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                                    <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                                    <td>{{ $j->guru->nama ?? '-' }}</td>
                                    <td>{{ $j->ruang ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"></div>
                    <div class="empty-state-text">Tidak ada jadwal pelajaran hari ini.</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"> Status Presensi Saya Hari Ini</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($statusPresensi->count() > 0)
                <div class="table-wrapper" style="border:none; border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mapel</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statusPresensi as $p)
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
                                    <td class="fw-bold">{{ $p->jurnal->mapel ?? '-' }}</td>
                                    <td><span class="badge {{ $badgeCls }}">{{ strtoupper($p->status) }}</span></td>
                                    <td>{{ $p->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"></div>
                    <div class="empty-state-text">Belum ada catatan presensi hari ini.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    
    <div>Data siswa tidak terhubung dengan akun Anda.</div>
</div>
@endif
@endsection
