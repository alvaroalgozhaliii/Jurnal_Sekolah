@extends('layouts.app')

@section('title', 'Tambah Jurusan Baru — Jurnal Sekolah')
@section('page-title', 'Tambah Jurusan Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Jurusan Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Master Jurusan / Program Keahlian</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Jurusan</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('jurusan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama_jurusan">Nama Jurusan <span class="req">*</span></label>
                <input type="text" id="nama_jurusan" name="nama_jurusan" value="{{ old('nama_jurusan') }}"
                    class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                <small class="text-muted">Nama lengkap program keahlian</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="rombel">Kode / Singkatan Jurusan <span class="req">*</span></label>
                <input type="text" id="rombel" name="rombel" value="{{ old('rombel') }}"
                    class="form-control" placeholder="Contoh: RPL" required maxlength="10">
                <small class="text-muted">Singkatan jurusan, maks 10 karakter (contoh: RPL, TKJ, MM)</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="maks_rombel">Maksimal Rombongan Belajar <span class="req">*</span></label>
                <input type="number" id="maks_rombel" name="maks_rombel" value="{{ old('maks_rombel', 1) }}"
                    class="form-control" min="1" max="20" required>
                <small class="text-muted">Jumlah kelas/rombel maksimal untuk jurusan ini</small>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN JURUSAN</button>
                <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

