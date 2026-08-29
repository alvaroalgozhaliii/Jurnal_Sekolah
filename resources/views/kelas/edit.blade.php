@extends('layouts.app')

@section('title', 'Edit Kelas — Jurnal Sekolah')
@section('page-title', 'Edit Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Kelas: {{ $kelas->nama_kelas }}</h1>
        <p class="page-subtitle">Memperbarui Informasi Master Kelas</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Kelas</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('kelas.update', $kelas->id_kelas) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nama_kelas">Nama Kelas <span class="req">*</span></label>
                    <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tingkat">Tingkat <span class="req">*</span></label>
                    <select id="tingkat" name="tingkat" class="form-control" required>
                        <option value="X" {{ $kelas->tingkat == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ $kelas->tingkat == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ $kelas->tingkat == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="id_jurusan">Jurusan</label>
                <select id="id_jurusan" name="id_jurusan" class="form-control select-search" placeholder="Ketik / Pilih Jurusan...">
                    <option value="">-- Tidak Ada Jurusan --</option>
                    @foreach($jurusan as $j)
                    <option value="{{ $j->id_jurusan }}" {{ $kelas->id_jurusan == $j->id_jurusan ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ $j->rombel }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="wali_kelas">Nama Wali Kelas</label>
                <input type="text" id="wali_kelas" name="wali_kelas" value="{{ old('wali_kelas', $kelas->wali_kelas) }}" class="form-control">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE KELAS</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection