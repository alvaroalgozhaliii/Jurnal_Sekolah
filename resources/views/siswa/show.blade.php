@extends('layouts.app')

@section('title', 'Detail Siswa — Jurnal Sekolah')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Siswa: {{ $siswa->nama }}</h1>
        <p class="page-subtitle">Informasi Lengkap Profil Siswa & Orang Tua</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('siswa.edit', $siswa->id_siswa) }}" class="btn btn-primary">Edit Siswa</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Profil Siswa</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>NISN</th><td class="fw-bold text-navy">{{ $siswa->NISN }}</td></tr>
                <tr><th>Nama Lengkap</th><td class="fw-bold">{{ $siswa->nama }}</td></tr>
                <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></td></tr>
                <tr><th>Jurusan</th><td>{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                <tr><th>Jenis Kelamin</th><td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td></tr>
                <tr><th>Tempat, Tanggal Lahir</th><td>{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ?? '-' }}</td></tr>
                <tr><th>No Telp Ortu</th><td>{{ $siswa->no_telp_ortu ?? '-' }}</td></tr>
                <tr><th>Status Siswa</th><td>
                    @if($siswa->aktif)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td></tr>
                <tr><th>Akun User Ortu</th><td>
                    @if($siswa->user)
                        <span class="badge badge-success">{{ $siswa->user->username }}</span>
                    @else
                        <span class="badge badge-gray">Belum ada akun</span>
                    @endif
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection