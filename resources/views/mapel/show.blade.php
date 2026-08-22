@extends('layouts.app')

@section('title', 'Detail Mapel — Jurnal Sekolah')
@section('page-title', 'Detail Mapel')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Mata Pelajaran: {{ $mapel->nama_mapel }}</h1>
        <p class="page-subtitle">Informasi Master Mata Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('mapel.edit', $mapel->id_mapel) }}" class="btn btn-primary">Edit Mapel</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Mapel</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>Nama Mapel</th><td class="fw-bold text-navy">{{ $mapel->nama_mapel }}</td></tr>
                <tr><th>Kode Mapel</th><td>{{ $mapel->kode_mapel ?? '-' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
