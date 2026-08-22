@extends('layouts.app')

@section('title', 'Edit Jadwal — Jurnal Sekolah')
@section('page-title', 'Edit Jadwal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jadwal Pelajaran</h1>
        <p class="page-subtitle">Memperbarui Informasi Jadwal KBM</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Data Jadwal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal.update', $jadwal->id_jadwal) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="hari">Hari <span class="req">*</span></label>
                    <select id="hari" name="hari" class="form-control" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                        <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_ke">Jam Ke <span class="req">*</span></label>
                    <input type="number" id="jam_ke" name="jam_ke" value="{{ $jadwal->jam_ke }}" min="1" max="15" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                    <select id="id_kelas" name="id_kelas" class="form-control" required>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ $jadwal->id_kelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_guru">Guru Pengajar</label>
                    <select id="id_guru" name="id_guru" class="form-control">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ $jadwal->id_guru == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="mapel">Mata Pelajaran <span class="req">*</span></label>
                <input type="text" id="mapel" name="mapel" value="{{ $jadwal->mapel }}" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ruang">Ruang Kelas</label>
                    <input type="text" id="ruang" name="ruang" value="{{ $jadwal->ruang }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="waktu_mulai">Waktu Mulai</label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" value="{{ $jadwal->waktu_mulai }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="waktu_selesai">Waktu Selesai</label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" value="{{ $jadwal->waktu_selesai }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="aktif">Status Jadwal</label>
                <select id="aktif" name="aktif" class="form-control">
                    <option value="1" {{ $jadwal->aktif ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$jadwal->aktif ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE JADWAL</button>
                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection