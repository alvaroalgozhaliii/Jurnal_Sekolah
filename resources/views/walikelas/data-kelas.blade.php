@extends('layouts.app')

@section('title', 'Data Kelas Saya — Jurnal Sekolah')
@section('page-title', 'Data Kelas Saya')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa Kelas Bimbingan</h1>
        <p class="page-subtitle">Daftar siswa terdaftar di kelas bimbingan wali kelas</p>
    </div>
</div>

@if($kelas)
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Informasi Kelas: {{ $kelas->nama_kelas }}</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Kelas:</span> <strong>{{ $kelas->nama_kelas }}</strong></div>
            <div><span class="text-muted">Jurusan:</span> <strong>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</strong></div>
            <div><span class="text-muted">Total Siswa:</span> <strong><span class="badge badge-navy">{{ $siswaList->count() }} Siswa</span></strong></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Siswa Kelas {{ $kelas->nama_kelas }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($siswaList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Status Hari Ini</th>
                        <th>Akun User Ortu</th>
                        <th>No Telp Ortu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $index => $s)
                    @php
                        $absen = $statusHariIni[$s->id_siswa] ?? null;
                        $st = $absen ? strtolower($absen->status) : null;
                        $stBadge = match($st) {
                            'hadir' => 'badge-success',
                            'sakit' => 'badge-purple',
                            'izin' => 'badge-info',
                            'alpa' => 'badge-danger',
                            'terlambat' => 'badge-warning',
                            default => 'badge-gray'
                        };
                        $stText = match($st) {
                            'hadir' => 'HADIR',
                            'sakit' => 'IZIN SAKIT',
                            'izin' => 'IZIN',
                            'alpa' => 'ALPA',
                            'terlambat' => 'TERLAMBAT',
                            default => 'Belum Absen'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="text-muted fw-bold">{{ $s->nis }}</td>
                        <td class="fw-bold text-navy">{{ $s->nama }}</td>
                        <td>
                            @if($s->jenis_kelamin == 'L')
                                <span class="badge badge-info">Laki-laki</span>
                            @elseif($s->jenis_kelamin == 'P')
                                <span class="badge badge-purple">Perempuan</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $stBadge }}">{{ $stText }}</span>
                            @if($absen && $absen->keterangan)
                                <div class="text-muted" style="font-size:11px; margin-top:2px;">{{ Str::limit($absen->keterangan, 30) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($s->user)
                                <span class="badge badge-success">{{ $s->user->username }}</span>
                            @else
                                <span class="badge badge-gray">Belum ada</span>
                            @endif
                        </td>
                        <td>{{ $s->no_telp_ortu ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada siswa di kelas ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Anda belum terdaftar sebagai Wali Kelas.</div>
</div>
@endif
@endsection
