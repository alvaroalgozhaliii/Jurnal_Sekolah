@extends('layouts.app')

@section('title', 'Edit Siswa — Jurnal Sekolah')
@section('page-title', 'Edit Data Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Data Siswa</h1>
        <p class="page-subtitle">Memperbarui Informasi Profil Siswa</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Edit Siswa: {{ $siswa->nama }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('siswa.update', $siswa->id_siswa) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="NISN">NISN <span class="req">*</span></label>
                    <input type="text" id="NISN" name="NISN" value="{{ old('NISN', $siswa->NISN) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                    <select id="id_kelas" name="id_kelas" class="form-control select-search" required placeholder="Ketik / Pilih Kelas...">
                        @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ $siswa->id_kelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan->nama_jurusan ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_telp_ortu">No Telp Ortu / Wali</label>
                    <input type="text" id="no_telp_ortu" name="no_telp_ortu" value="{{ old('no_telp_ortu', $siswa->no_telp_ortu) }}" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="aktif">Status Keaktifan</label>
                <select id="aktif" name="aktif" class="form-control">
                    <option value="1" {{ $siswa->aktif ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$siswa->aktif ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE DATA SISWA</button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection