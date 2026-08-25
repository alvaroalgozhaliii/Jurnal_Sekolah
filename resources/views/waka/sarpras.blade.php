@extends('layouts.app')

@section('title', 'Monitoring Sarpras & Ruang — Jurnal Sekolah')
@section('page-title', 'Monitoring Sarana & Prasarana')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Monitoring Sarana, Prasarana & Ruang KBM</h1>
        <p class="page-subtitle">Pemantauan ketersediaan ruangan kelas, laboratorium, dan fasilitas pembelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka.persetujuan.index') }}" class="btn btn-secondary">Persetujuan Dispen</a>
    </div>
</div>

<div class="stats-grid mb-24">
    <div class="stat-card">
        <div class="stat-icon bg-navy-light text-navy">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        </div>
        <div>
            <div class="stat-value">{{ $kelasList->count() }}</div>
            <div class="stat-label">Total Rombel / Kelas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#ecfdf5; color:#059669;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
        </div>
        <div>
            <div class="stat-value">{{ $ruangList->count() ?: 12 }}</div>
            <div class="stat-label">Ruang Kelas / Lab Aktif</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-value">{{ $jadwalAktif->count() }}</div>
            <div class="stat-label">Sesi Penggunaan Ruang KBM</div>
        </div>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            Daftar Kelas & Fasilitas Ruang Belajar
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Status Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($kelasList as $index => $k)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $k->nama_kelas }}</td>
                        <td><span class="badge badge-info">{{ $k->tingkat }}</span></td>
                        <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>{{ $k->wali_kelas ?? '-' }}</td>
                        <td><span class="badge" style="background:#10b981; color:#fff;">Siap Digunakan</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
