@extends('layouts.app')

@section('title', 'Detail Jurusan — Jurnal Sekolah')
@section('page-title', 'Detail Jurusan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Jurusan: {{ $jurusan->nama_jurusan }}</h1>
        <p class="page-subtitle">Informasi Master Jurusan & Daftar Kelas Terkait</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('jurusan.edit', $jurusan->id_jurusan) }}" class="btn btn-primary">Edit Jurusan</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Jurusan</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>Nama Jurusan</th><td class="fw-bold text-navy">{{ $jurusan->nama_jurusan }}</td></tr>
                <tr><th>Kode Jurusan</th><td>{{ $jurusan->kode_jurusan ?? '-' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
