@extends('layouts.app')

@section('title', 'Edit Pengguna — Jurnal Sekolah')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Akun Pengguna: {{ $pengguna->username }}</h1>
        <p class="page-subtitle">Memperbarui Informasi Login, Role User & No WhatsApp</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Akun</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('pengguna.update', $pengguna->id_user) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $pengguna->nama) }}" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="username">Username <span class="req">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $pengguna->username) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role">Role / Peran <span class="req">*</span></label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="admin" {{ $pengguna->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ $pengguna->role == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="piket" {{ $pengguna->role == 'piket' ? 'selected' : '' }}>Piket</option>
                        <option value="ortu" {{ $pengguna->role == 'ortu' ? 'selected' : '' }}>Ortu</option>
                        <option value="walikelas" {{ $pengguna->role == 'walikelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="waka_kesiswaan" {{ $pengguna->role == 'waka_kesiswaan' ? 'selected' : '' }}>Waka Kesiswaan</option>
                        <option value="waka_sdm" {{ $pengguna->role == 'waka_sdm' ? 'selected' : '' }}>Waka SDM</option>
                        <option value="waka_kurikulum" {{ $pengguna->role == 'waka_kurikulum' ? 'selected' : '' }}>Waka Kurikulum</option>
                        <option value="kepala_sekolah" {{ $pengguna->role == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="satpam" {{ $pengguna->role == 'satpam' ? 'selected' : '' }}>Satpam</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password Baru (Opsional)</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_hp">No WhatsApp / HP</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $pengguna->no_hp) }}" class="form-control" placeholder="Contoh: 085707300240">
                </div>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE AKUN</button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
