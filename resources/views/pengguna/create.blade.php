@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru — Jurnal Sekolah')
@section('page-title', 'Tambah Pengguna Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Akun Pengguna Baru</h1>
        <p class="page-subtitle">Formulir Pembuatan Akun Login System</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Akun</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="Nama Lengkap Pemilik Akun" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="username">Username <span class="req">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username login" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role">Role / Peran <span class="req">*</span></label>
                    <select id="role" name="role" class="form-control select-search" required placeholder="Ketik / Pilih Role...">
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="piket" {{ old('role') == 'piket' ? 'selected' : '' }}>Piket</option>
                        <option value="ortu" {{ old('role') == 'ortu' ? 'selected' : '' }}>Ortu</option>
                        <option value="walikelas" {{ old('role') == 'walikelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="waka_kesiswaan" {{ old('role') == 'waka_kesiswaan' ? 'selected' : '' }}>Waka Kesiswaan</option>
                        <option value="waka_sdm" {{ old('role') == 'waka_sdm' ? 'selected' : '' }}>Waka SDM</option>
                        <option value="waka_kurikulum" {{ old('role') == 'waka_kurikulum' ? 'selected' : '' }}>Waka Kurikulum</option>
                        <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="satpam" {{ old('role') == 'satpam' ? 'selected' : '' }}>Satpam</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="req">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password akun" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_hp">No WhatsApp / HP</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="form-control" placeholder="Contoh: 085707300240">
                </div>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN AKUN</button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
