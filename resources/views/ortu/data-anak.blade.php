@extends('layouts.app')

@section('title', 'Data Anak — Jurnal Sekolah')
@section('page-title', 'Data Anak')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Profil Anak</h1>
        <p class="page-subtitle">Informasi Detail Data Siswa Terhubung</p>
    </div>
</div>

@if($anakList->count() > 1)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('ortu.data-anak') }}" method="GET" class="d-flex align-center gap-12">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Anak:</label>
            <select name="id_siswa" onchange="this.form.submit()" class="form-control" style="max-width:350px;">
                @foreach($anakList as $a)
                <option value="{{ $a->id_siswa }}" {{ ($selectedSiswa && $selectedSiswa->id_siswa == $a->id_siswa) ? 'selected' : '' }}>
                    {{ $a->nama }} (NISN: {{ $a->NISN }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

@if($selectedSiswa)
<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3 class="card-title">Profil Siswa: {{ $selectedSiswa->nama }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="info-table">
            <tbody>
                <tr><th>NISN</th><td class="fw-bold text-navy">{{ $selectedSiswa->NISN }}</td></tr>
                <tr><th>Nama Lengkap</th><td class="fw-bold">{{ $selectedSiswa->nama }}</td></tr>
                <tr><th>Kelas</th><td><span class="badge badge-navy">{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</span></td></tr>
                <tr><th>Jurusan</th><td>{{ $selectedSiswa->kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                <tr><th>Jenis Kelamin</th><td>{{ $selectedSiswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($selectedSiswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td></tr>
                <tr><th>Tempat, Tanggal Lahir</th><td>{{ $selectedSiswa->tempat_lahir ?? '-' }}, {{ $selectedSiswa->tanggal_lahir ?? '-' }}</td></tr>
                <tr><th>No Telp Ortu</th><td>{{ $selectedSiswa->no_telp_ortu ?? '-' }}</td></tr>
                <tr><th>Status Siswa</th><td>
                    @if($selectedSiswa->aktif)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Data anak tidak ditemukan.</div>
</div>
@endif
@endsection
