@extends('layouts.app')

@section('title', 'Tambah Kelas — Jurnal Sekolah')
@section('page-title', 'Tambah Kelas Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Kelas Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Master Kelas Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Kelas</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('kelas.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nama_kelas">Nama Kelas <span class="req">*</span></label>
                    <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" class="form-control" placeholder="Contoh: X RPL 1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tingkat">Tingkat <span class="req">*</span></label>
                    <select id="tingkat" name="tingkat" class="form-control" required>
                        <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="id_jurusan">Jurusan</label>
                <select id="id_jurusan" name="id_jurusan" class="form-control select-search" placeholder="Ketik / Pilih Jurusan...">
                    <option value="">Tidak Ada Jurusan</option>
                    @foreach($jurusan as $j)
                    <option value="{{ $j->id_jurusan }}" {{ old('id_jurusan') == $j->id_jurusan ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ $j->rombel }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="wali_kelas">Nama Wali Kelas</label>
                <input type="text" id="wali_kelas" name="wali_kelas" value="{{ old('wali_kelas') }}" class="form-control" placeholder="Nama lengkap Wali Kelas">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN KELAS</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection