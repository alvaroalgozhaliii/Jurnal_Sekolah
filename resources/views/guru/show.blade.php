@extends('layouts.app')

@section('title', 'Detail Guru — Jurnal Sekolah')
@section('page-title', 'Detail Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Guru: {{ $guru->nama }}</h1>
        <p class="page-subtitle">Informasi Lengkap Profil & Jadwal Mengajar Guru</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('guru.edit', $guru->id_guru) }}" class="btn btn-primary">Edit Profil Guru</a>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil Guru</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="info-table">
                <tbody>
                    <tr><th>Nama Lengkap</th><td class="fw-bold text-navy">{{ $guru->nama }}</td></tr>
                    <tr><th>NIP</th><td>{{ $guru->nip ?? '-' }}</td></tr>
                    <tr><th>Bidang Studi</th><td>{{ $guru->bidang_studi ?? '-' }}</td></tr>
                    <tr><th>No Telepon</th><td>{{ $guru->no_telp ?? '-' }}</td></tr>
                    <tr><th>Akun User</th><td>
                        @if($guru->user)
                            <span class="badge badge-success">{{ $guru->user->username }}</span>
                        @else
                            <span class="badge badge-gray">Belum ada akun</span>
                        @endif
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Jadwal Mengajar ({{ $guru->jadwal ? $guru->jadwal->count() : 0 }} Sesi)</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($guru->jadwal && $guru->jadwal->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Waktu</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guru->jadwal as $j)
                        <tr>
                            <td class="fw-bold">{{ $j->hari }}</td>
                            <td>Jam ke-{{ $j->jam_ke }}</td>
                            <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                            <td><span class="badge badge-navy">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                            <td class="text-navy fw-bold">{{ $j->mapel }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Belum ada jadwal mengajar.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection