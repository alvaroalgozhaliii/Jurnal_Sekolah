@extends('layouts.app')

@section('title', 'Tambah Mapel — Jurnal Sekolah')
@section('page-title', 'Tambah Mapel Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Mata Pelajaran Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Master Mata Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Mapel</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('mapel.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama_mapel">Nama Mata Pelajaran <span class="req">*</span></label>
                <input type="text" id="nama_mapel" name="nama_mapel" value="{{ old('nama_mapel') }}" class="form-control" placeholder="Contoh: Matematika Wajib" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="kode_mapel">Kode Mapel</label>
                <input type="text" id="kode_mapel" name="kode_mapel" value="{{ old('kode_mapel') }}" class="form-control" placeholder="Contoh: MTK">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN MAPEL</button>
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
