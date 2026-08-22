@extends('layouts.app')

@section('title', 'Input Absensi Siswa — Jurnal Sekolah')
@section('page-title', 'Input Absensi Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Input Absensi Siswa (Batch)</h1>
        <p class="page-subtitle">Pilih jurnal dan catat kehadiran siswa sekelas</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('absensi-siswa.create') }}" method="GET" class="d-flex align-center gap-12 flex-wrap">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Jurnal Harian:</label>
            <select name="id_jurnal" onchange="this.form.submit()" class="form-control" style="max-width:500px;">
                <option value="">-- Pilih Jurnal --</option>
                @foreach($jurnalList as $j)
                <option value="{{ $j->id_jurnal }}" {{ ($jurnalSelected && $jurnalSelected->id_jurnal == $j->id_jurnal) ? 'selected' : '' }}>
                    {{ $j->tanggal }} | {{ $j->mapel }} | Kelas {{ $j->jadwal->kelas->nama_kelas ?? '-' }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($jurnalSelected)
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Detail Jurnal Terpilih</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Jurnal:</span> <strong>{{ $jurnalSelected->tanggal }} - {{ $jurnalSelected->mapel }}</strong></div>
            <div><span class="text-muted">Kelas:</span> <strong><span class="badge badge-navy">{{ $jurnalSelected->jadwal->kelas->nama_kelas ?? '-' }}</span></strong></div>
            <div><span class="text-muted">Guru:</span> <strong>{{ $jurnalSelected->guru->nama ?? '-' }}</strong></div>
            <div style="grid-column: 1 / -1;"><span class="text-muted">Materi:</span> <strong>{{ $jurnalSelected->materi }}</strong></div>
        </div>
    </div>
</div>

@if($siswaList->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Presensi Siswa Kelas ({{ $siswaList->count() }} siswa)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form action="{{ route('absensi-siswa.storeBatch') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jurnal" value="{{ $jurnalSelected->id_jurnal }}">
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Status Kehadiran *</th>
                            <th>Jam Masuk (jika Terlambat)</th>
                            <th>Selisih Terlambat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($siswaList as $s)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $s->nis }}</td>
                        <td class="fw-bold text-navy">{{ $s->nama }}</td>
                        <td>
                            <select name="absensi[{{ $s->id_siswa }}]" class="form-control" required style="padding:4px 8px;">
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                        <td><input type="time" name="jam_masuk[{{ $s->id_siswa }}]" class="form-control" style="padding:4px 8px;"></td>
                        <td><input type="number" name="menit_terlambat[{{ $s->id_siswa }}]" min="0" placeholder="Menit" class="form-control" style="max-width:90px; padding:4px 8px;"></td>
                        <td><input type="text" name="keterangan[{{ $s->id_siswa }}]" placeholder="Keterangan opsional" class="form-control" style="padding:4px 8px;"></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-lg">SIMPAN ABSENSI BATCH</button>
            </div>
        </form>
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Tidak ada siswa di kelas ini.</div>
</div>
@endif
@else
<div class="alert alert-info">
    <div>Silakan pilih jurnal terlebih dahulu dari dropdown di atas.</div>
</div>
@endif
@endsection