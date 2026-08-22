@extends('layouts.app')

@section('title', 'Tambah Guru Baru — Jurnal Sekolah')
@section('page-title', 'Tambah Guru Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Guru Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Data Guru & Akun User</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Guru</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('guru.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="Contoh: Drs. Ahmad Wijaya" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nip">NIP</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}" class="form-control" placeholder="NIP (Opsional)">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bidang_studi">Bidang Studi</label>
                    <input type="text" id="bidang_studi" name="bidang_studi" value="{{ old('bidang_studi') }}" class="form-control" placeholder="Contoh: Matematika">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="no_telp">No Telepon</label>
                <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" class="form-control" placeholder="Nomor WhatsApp / HP">
            </div>

            <hr style="border:none; border-top: 1px solid var(--border); margin: 20px 0;">

            <h4 style="font-size: 14px; font-weight: 700; color: var(--navy); margin-bottom: 12px;">Akun User Login (Opsional)</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username login">
                    <small class="text-muted">Kosongkan jika tidak ingin membuat akun login</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password akun">
                    <small class="text-muted">Default: guru123 jika diisi kosong</small>
                </div>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN DATA GURU</button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection