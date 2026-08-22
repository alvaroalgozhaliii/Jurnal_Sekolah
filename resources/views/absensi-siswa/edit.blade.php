@extends('layouts.app')

@section('title', 'Edit Absensi Siswa — Jurnal Sekolah')
@section('page-title', 'Edit Absensi Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Absensi Siswa</h1>
        <p class="page-subtitle">Memperbarui status kehadiran siswa</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Edit Status Absensi</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-16">
            <div>
                <div><strong>Siswa:</strong> {{ $absensi->siswa->nama ?? '-' }}</div>
                <div><strong>Jurnal:</strong> {{ $absensi->jurnal->tanggal ?? '-' }} - {{ $absensi->jurnal->mapel ?? '-' }}</div>
            </div>
        </div>

        <form action="{{ route('absensi-siswa.update', $absensi->id_absensi) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="status">Status Kehadiran <span class="req">*</span></label>
                <select id="status" name="status" class="form-control" required>
                    <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpa" {{ $absensi->status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                    <option value="terlambat" {{ $absensi->status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="keterangan">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $absensi->keterangan) }}" class="form-control" placeholder="Catatan tambahan">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE ABSENSI</button>
                <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection