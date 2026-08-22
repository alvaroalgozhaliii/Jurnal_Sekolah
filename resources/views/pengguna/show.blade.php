@extends('layouts.app')

@section('title', 'Detail Pengguna — Jurnal Sekolah')
@section('page-title', 'Detail Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Akun: {{ $pengguna->username }}</h1>
        <p class="page-subtitle">Informasi Lengkap Hak Akses, No WhatsApp & Profil Terhubung</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('pengguna.edit', $pengguna->id_user) }}" class="btn btn-primary">Edit Akun</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Akun User</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>Nama Lengkap</th><td class="fw-bold text-navy">{{ $pengguna->nama }}</td></tr>
                <tr><th>Username</th><td><span class="badge badge-navy">{{ $pengguna->username }}</span></td></tr>
                <tr><th>Role / Peran</th><td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $pengguna->role)) }}</span></td></tr>
                <tr><th>No WhatsApp / HP</th><td><strong>{{ $pengguna->no_hp ?? '-' }}</strong></td></tr>
                <tr><th>Profil Terhubung</th><td>
                    @if($pengguna->guru)
                        <span class="badge badge-info">Guru: {{ $pengguna->guru->nama }}</span>
                    @elseif($pengguna->siswa)
                        <span class="badge badge-success">Siswa: {{ $pengguna->siswa->nama }}</span>
                    @else
                        <span class="badge badge-gray">Akun Standalone</span>
                    @endif
                </td></tr>
                <tr><th>Dibuat Pada</th><td class="text-muted">{{ $pengguna->created_at ?? '-' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
