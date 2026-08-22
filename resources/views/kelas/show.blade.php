@extends('layouts.app')

@section('title', 'Detail Kelas — Jurnal Sekolah')
@section('page-title', 'Detail Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Kelas: {{ $kelas->nama_kelas }}</h1>
        <p class="page-subtitle">Informasi Master Kelas & Daftar Siswa Terdaftar</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        <a href="{{ route('kelas.edit', $kelas->id_kelas) }}" class="btn btn-primary">Edit Kelas</a>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Kelas</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="info-table">
                <tbody>
                    <tr><th>Nama Kelas</th><td class="fw-bold text-navy">{{ $kelas->nama_kelas }}</td></tr>
                    <tr><th>Tingkat</th><td><span class="badge badge-navy">{{ $kelas->tingkat }}</span></td></tr>
                    <tr><th>Jurusan</th><td>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                    <tr><th>Wali Kelas</th><td>{{ $kelas->wali_kelas ?? '-' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa ({{ $kelas->siswa->count() }} Siswa)</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($kelas->siswa->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>JK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas->siswa as $s)
                        <tr>
                            <td class="no-col">{{ $loop->iteration }}</td>
                            <td class="text-muted fw-bold">{{ $s->nis }}</td>
                            <td class="fw-bold text-navy">{{ $s->nama }}</td>
                            <td>{{ $s->jenis_kelamin }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Belum ada siswa di kelas ini.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection