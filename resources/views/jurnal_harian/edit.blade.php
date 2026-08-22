@extends('layouts.app')

@section('title', 'Edit Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Edit Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jurnal Harian</h1>
        <p class="page-subtitle">Memperbarui catatan jurnal harian mengajar</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card mb-24" style="max-width: 700px;">
    <div class="card-header">
        <h3 class="card-title">Informasi KBM</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Jadwal:</span> <strong>{{ $jurnal_harian->jadwal->hari ?? '-' }} | Jam {{ $jurnal_harian->jadwal->jam_ke ?? '-' }}</strong></div>
            <div><span class="text-muted">Kelas:</span> <strong><span class="badge badge-navy">{{ $jurnal_harian->jadwal->kelas->nama_kelas ?? '-' }}</span></strong></div>
            <div><span class="text-muted">Mapel:</span> <strong>{{ $jurnal_harian->mapel }}</strong></div>
            <div><span class="text-muted">Tanggal:</span> <strong>{{ $jurnal_harian->tanggal }}</strong></div>
            <div><span class="text-muted">Guru:</span> <strong>{{ $jurnal_harian->guru->nama ?? '-' }}</strong></div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h3 class="card-title">Edit Formulir Jurnal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurnal-harian.update', $jurnal_harian->id_jurnal) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="materi">Materi Pelajaran <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi', $jurnal_harian->materi) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="sub_materi">Sub Materi / Pokok Bahasan</label>
                <input type="text" id="sub_materi" name="sub_materi" value="{{ old('sub_materi', $jurnal_harian->sub_materi) }}" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label" for="catatan_pengajaran">Catatan Pengajaran</label>
                <textarea id="catatan_pengajaran" name="catatan_pengajaran" class="form-control" rows="4">{{ old('catatan_pengajaran', $jurnal_harian->catatan_pengajaran) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="status_keterlaksanaan">Status Keterlaksanaan</label>
                <select id="status_keterlaksanaan" name="status_keterlaksanaan" class="form-control">
                    <option value="terlaksana" {{ $jurnal_harian->status_keterlaksanaan == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                    <option value="tidak_terlaksana" {{ $jurnal_harian->status_keterlaksanaan == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                    <option value="kosong" {{ $jurnal_harian->status_keterlaksanaan == 'kosong' ? 'selected' : '' }}>Kosong</option>
                    <option value="pengganti" {{ $jurnal_harian->status_keterlaksanaan == 'pengganti' ? 'selected' : '' }}>Pengganti</option>
                </select>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE JURNAL</button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection