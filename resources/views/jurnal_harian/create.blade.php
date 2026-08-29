@extends('layouts.app')

@section('title', 'Isi Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Isi Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Formulir Jurnal Harian KBM</h1>
        <p class="page-subtitle">Pilih jadwal mengajar dan lengkapi catatan KBM</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('jurnal-harian.create') }}" method="GET" class="d-flex align-center gap-12 flex-wrap">
            <label for="id_jadwal" class="form-label" style="margin:0; white-space:nowrap;">Pilih Jadwal Mengajar:</label>
            <select id="id_jadwal" name="id_jadwal" onchange="this.form.submit()" class="form-control select-search" style="min-width:450px;" placeholder="Ketik / Cari Jadwal Mengajar KBM...">
                <option value="">-- Pilih Jadwal KBM Hari Ini --</option>
                @foreach($jadwalList as $j)
                <option value="{{ $j->id_jadwal }}" {{ ($jadwalSelected && $jadwalSelected->id_jadwal == $j->id_jadwal) ? 'selected' : '' }}>
                    {{ $j->hari }} | Jam {{ $j->jam_ke }} ({{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) }}) | Kelas {{ $j->kelas->nama_kelas ?? '-' }} | {{ $j->mapel }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($jadwalSelected)
<div class="card mb-24" style="max-width: 750px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Jadwal Terpilih</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Kelas:</span> <strong><span class="badge badge-navy">{{ $jadwalSelected->kelas->nama_kelas ?? '-' }}</span></strong></div>
            <div><span class="text-muted">Mata Pelajaran:</span> <strong>{{ $jadwalSelected->mapel }}</strong></div>
            <div><span class="text-muted">Jam Pembelajaran:</span> <strong>Jam {{ $jadwalSelected->jam_ke }} — {{ \App\Services\KbmService::getLabelWaktu($jadwalSelected->hari, $jadwalSelected->jam_ke) ?: ($jadwalSelected->waktu_mulai . ' - ' . $jadwalSelected->waktu_selesai) }}</strong></div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 750px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Catatan Jurnal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurnal-harian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jadwal" value="{{ $jadwalSelected->id_jadwal }}">
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">

            <div class="form-group">
                <label class="form-label" for="materi">Materi Pelajaran Utam <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi') }}" class="form-control" placeholder="Contoh: Bab 3 Persamaan Kuadrat" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="sub_materi">Sub Materi / Pokok Bahasan</label>
                <input type="text" id="sub_materi" name="sub_materi" value="{{ old('sub_materi') }}" class="form-control" placeholder="Contoh: Rumus ABC dan Diskriminan">
            </div>

            <div class="form-group">
                <label class="form-label" for="catatan_pengajaran">Catatan Pengajaran & Evaluasi Kelas</label>
                <textarea id="catatan_pengajaran" name="catatan_pengajaran" class="form-control" rows="4" placeholder="Catatan respon siswa, keaktifan, atau tugas KBM">{{ old('catatan_pengajaran') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="status_keterlaksanaan">Status Keterlaksanaan <span class="req">*</span></label>
                <select id="status_keterlaksanaan" name="status_keterlaksanaan" class="form-control" required>
                    <option value="terlaksana" {{ old('status_keterlaksanaan') == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                    <option value="tidak_terlaksana" {{ old('status_keterlaksanaan') == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                    <option value="kosong" {{ old('status_keterlaksanaan') == 'kosong' ? 'selected' : '' }}>Kosong</option>
                    <option value="pengganti" {{ old('status_keterlaksanaan') == 'pengganti' ? 'selected' : '' }}>Pengganti</option>
                </select>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary btn-lg">SIMPAN JURNAL & LANJUT ABSENSI SISWA</button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@else
<div class="alert alert-info">
    <div>Silakan pilih jadwal mengajar terlebih dahulu dari dropdown di atas.</div>
</div>
@endif
@endsection