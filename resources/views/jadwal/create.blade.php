@extends('layouts.app')

@section('title', 'Tambah Jadwal — Jurnal Sekolah')
@section('page-title', 'Tambah Jadwal Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Jadwal Baru</h1>
        <p class="page-subtitle">Formulir Pembuatan Jadwal Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Jadwal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="hari">Hari <span class="req">*</span></label>
                    <select id="hari" name="hari" class="form-control" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                        <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_ke">Jam Ke <span class="req">*</span></label>
                    <input type="number" id="jam_ke" name="jam_ke" value="{{ old('jam_ke') }}" min="1" max="15" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                    <select id="id_kelas" name="id_kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_guru">Guru Pengajar</label>
                    <select id="id_guru" name="id_guru" class="form-control">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="mapel">Mata Pelajaran <span class="req">*</span></label>
                <input type="text" id="mapel" name="mapel" value="{{ old('mapel') }}" class="form-control" required placeholder="Contoh: Bahasa Indonesia">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ruang">Ruang Kelas</label>
                    <input type="text" id="ruang" name="ruang" value="{{ old('ruang') }}" class="form-control" placeholder="Contoh: R.101">
                </div>
                <div class="form-group">
                    <label class="form-label" for="waktu_mulai">Waktu Mulai</label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="waktu_selesai">Waktu Selesai</label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN JADWAL</button>
                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection