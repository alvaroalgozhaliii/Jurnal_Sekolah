@extends('layouts.app')

@section('title', 'Tambah Siswa — Jurnal Sekolah')
@section('page-title', 'Tambah Siswa Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Siswa Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Siswa & Pembuatan Akun Ortu</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Siswa</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nis">NIS <span class="req">*</span></label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis') }}" class="form-control" placeholder="Nomor Induk Siswa" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                    <select id="id_kelas" name="id_kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="Nama Lengkap Siswa" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_telp_ortu">No Telp Ortu / Wali</label>
                    <input type="text" id="no_telp_ortu" name="no_telp_ortu" value="{{ old('no_telp_ortu') }}" class="form-control" placeholder="Nomor WhatsApp Ortu">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-control" placeholder="Kota Lahir">
                </div>
                <div class="form-group">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control">
                </div>
            </div>

            <hr style="border:none; border-top: 1px solid var(--border); margin: 20px 0;">

            <h4 style="font-size: 14px; font-weight: 700; color: var(--navy); margin-bottom: 12px;">Akun User Ortu (Opsional)</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="username">Username Akun</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username Ortu/Siswa">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password akun">
                    <small class="text-muted">Default: siswa123</small>
                </div>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN DATA SISWA</button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
