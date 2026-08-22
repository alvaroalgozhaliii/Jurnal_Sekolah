@extends('layouts.app')

@section('title', 'Edit Mapel — Jurnal Sekolah')
@section('page-title', 'Edit Mapel')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Mata Pelajaran: {{ $mapel->nama_mapel }}</h1>
        <p class="page-subtitle">Memperbarui Informasi Master Mapel</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Mapel</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('mapel.update', $mapel->id_mapel) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="nama_mapel">Nama Mata Pelajaran <span class="req">*</span></label>
                <input type="text" id="nama_mapel" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="kode_mapel">Kode Mapel</label>
                <input type="text" id="kode_mapel" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" class="form-control">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE MAPEL</button>
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
