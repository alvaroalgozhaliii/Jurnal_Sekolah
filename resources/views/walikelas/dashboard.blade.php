@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas — Jurnal Sekolah')
@section('page-title', 'Dashboard Wali Kelas')

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-12">
    <div>
        <h1 class="page-title">Dashboard Wali Kelas</h1>
        <p class="page-subtitle">Monitoring Kehadiran, Kedisiplinan &amp; Presensi Siswa Bimbingan</p>
    </div>
    @if($kelas)
    <div class="page-actions" style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('walikelas.data-kelas') }}" class="btn btn-secondary">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
            Data Siswa
        </a>
        <a href="{{ route('walikelas.rekap-presensi') }}" class="btn btn-secondary">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            Rekap Presensi
        </a>
        <a href="{{ route('walikelas.siswa-terlambat') }}" class="btn btn-secondary">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Siswa Terlambat
        </a>
        <a href="{{ route('walikelas.jurnal') }}" class="btn btn-primary" style="background:#059669; border-color:#059669;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            Jurnal KBM Kelas
        </a>
    </div>
    @endif
</div>

@include('partials.kbm-clock-banner')

@if($kelas)
<!-- INFO KELAS BIMBINGAN CARD -->
<div class="card mb-24" style="border-left: 4px solid #059669; overflow:hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.08), rgba(59, 130, 246, 0.03)); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:14px 20px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#059669; color:#fff; display:flex; align-items:center; justify-content:center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div>
                <h3 class="card-title" style="margin:0; font-size:17px; font-weight:700; color:#059669;">
                    Rombongan Belajar: Kelas {{ $kelas->nama_kelas }}
                </h3>
                <p style="margin:2px 0 0; font-size:12.5px; color:var(--text-muted);">
                    Tingkat {{ $kelas->tingkat }} &bull; Jurusan: {{ $kelas->jurusan->nama_jurusan ?? 'Reguler' }} &bull; Wali Kelas: <strong>{{ $kelas->wali_kelas ?? Auth::user()->nama }}</strong>
                </p>
            </div>
        </div>
        <div>
            <span class="badge badge-navy" style="font-size:13px; padding:6px 14px; border-radius:8px;">
                Total: {{ $totalSiswa }} Siswa
            </span>
        </div>
    </div>
</div>

<!-- STAT CARDS KEHADIRAN SISWA HARI INI -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:14px; margin-bottom:24px;">
    <div class="stat-card" style="border-left: 4px solid #1e3a8a;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #1e3a8a;">{{ $totalSiswa }}</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #10b981;">{{ $hadirCount }}</div>
            <div class="stat-label">Hadir Hari Ini</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #f59e0b;">{{ $terlambatCount + ($terlambatHariIni ? $terlambatHariIni->count() : 0) }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #3b82f6;">{{ $izinCount + $sakitCount + ($izinHariIni ? $izinHariIni->count() : 0) }}</div>
            <div class="stat-label">Izin / Sakit</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-icon-box red">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #ef4444;">{{ $alpaCount }}</div>
            <div class="stat-label">Alpa / Tanpa Keterangan</div>
        </div>
    </div>
</div>

{{-- LOG SISWA TERLAMBAT HARI INI (DARI PIKET) --}}
@if(isset($terlambatHariIni) && $terlambatHariIni->count() > 0)
<div class="card mb-24" style="border-left: 4px solid #f59e0b;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="card-title" style="margin:0; font-size:15px; color:#b45309; font-weight:700;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:6px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Siswa Bimbingan Terlambat Hari Ini ({{ $terlambatHariIni->count() }} Siswa)
        </h3>
        <a href="{{ route('walikelas.siswa-terlambat') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">Lihat Selengkapnya &rarr;</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead><tr><th>No</th><th>NISN</th><th>Nama Siswa</th><th>Jam Tiba</th><th>Alasan</th><th>Pencatat</th></tr></thead>
                <tbody>
                    @foreach($terlambatHariIni as $idx => $st)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $st->siswa->NISN ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $st->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-warning">{{ $st->jam_kedatangan ?? '-' }}</span></td>
                        <td>{{ $st->alasan ?? '-' }}</td>
                        <td class="text-muted">{{ $st->petugasPiket->nama ?? 'Petugas Piket' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- PENGAJUAN IZIN / SAKIT SISWA HARI INI --}}
@if(isset($izinHariIni) && $izinHariIni->count() > 0)
<div class="card mb-24" style="border-left: 4px solid #3b82f6;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="card-title" style="margin:0; font-size:15px; color:#1d4ed8; font-weight:700;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
            Pengajuan Izin / Sakit Hari Ini ({{ $izinHariIni->count() }} Siswa)
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead><tr><th>No</th><th>Nama Siswa</th><th>Kategori</th><th>Alasan</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($izinHariIni as $idx => $pi)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $pi->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $pi->kategori)) }}</span></td>
                        <td>{{ $pi->alasan ?? '-' }}</td>
                        <td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $pi->status)) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- PRESENSI SISWA KELAS HARI INI DARI KBM -->
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <h3 class="card-title" style="margin:0;">
            Presensi Siswa Kelas Hari Ini (KBM)
        </h3>
        <a href="{{ route('walikelas.rekap-presensi') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">Rekap Presensi Lengkap &rarr;</a>
    </div>
    <div class="card-body" style="padding:0;">
        @if($presensiHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0; width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch;">
            <table class="table" style="min-width:600px; width:100%;">
                <thead><tr><th>No</th><th>NISN</th><th>Nama Siswa</th><th>Status</th><th>Jam Masuk</th><th>Keterangan</th></tr></thead>
                <tbody>
                @foreach($presensiHariIni as $idx => $p)
                @php
                    $st = strtolower($p->status);
                    $badgeCls = match($st) {
                        'hadir' => 'badge-success',
                        'izin' => 'badge-info',
                        'sakit' => 'badge-purple',
                        'alpa' => 'badge-danger',
                        'terlambat' => 'badge-warning',
                        default => 'badge-gray'
                    };
                @endphp
                <tr>
                    <td class="text-muted">{{ $idx + 1 }}</td>
                    <td class="text-muted">{{ $p->siswa->NISN ?? '-' }}</td>
                    <td class="fw-bold text-navy">{{ $p->siswa->nama ?? '-' }}</td>
                    <td><span class="badge {{ $badgeCls }}">{{ strtoupper($p->status) }}</span></td>
                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--text-muted);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div class="empty-state-text">Belum ada presensi KBM yang dicatat untuk siswa kelas ini hari ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
    <div>Anda belum ditugaskan sebagai Wali Kelas pada rombongan belajar tertentu. Hubungi Administrator untuk menetapkan penugasan wali kelas Anda.</div>
</div>
@endif
@endsection
