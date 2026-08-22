@extends('layouts.app')

@section('title', 'Edit Jurusan — Jurnal Sekolah')
@section('page-title', 'Edit Jurusan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jurusan: {{ $jurusan->nama_jurusan }}</h1>
        <p class="page-subtitle">Memperbarui Informasi Master Jurusan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Jurusan</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurusan.update', $jurusan->id_jurusan) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nama_jurusan">Nama Jurusan <span class="req">*</span></label>
                <input type="text" id="nama_jurusan" name="nama_jurusan" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="kode_jurusan">Kode Jurusan</label>
                <input type="text" id="kode_jurusan" name="kode_jurusan" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}" class="form-control">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE JURUSAN</button>
                <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
