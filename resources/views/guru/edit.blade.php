@extends('layouts.app')

@section('title', 'Edit Data Guru — Jurnal Sekolah')
@section('page-title', 'Edit Data Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Data Guru</h1>
        <p class="page-subtitle">Memperbarui Informasi Profil Guru</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Guru: {{ $guru->nama }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('guru.update', $guru->id_guru) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nip">NIP</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bidang_studi">Bidang Studi</label>
                    <input type="text" id="bidang_studi" name="bidang_studi" value="{{ old('bidang_studi', $guru->bidang_studi) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="no_telp">No Telepon</label>
                <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $guru->no_telp) }}" class="form-control">
            </div>

            @if($guru->user)
                <div class="alert alert-info mt-16" style="margin-bottom:0;">
                    <div>Akun User Terhubung: <strong>{{ $guru->user->username }}</strong> (Role: {{ strtoupper($guru->user->role) }})</div>
                </div>
            @endif

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE DATA GURU</button>
                <a href="{{ route('guru.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection