@extends('layouts.app')

@section('title', 'Dashboard Waka Kurikulum — Jurnal Sekolah')
@section('page-title', 'Dashboard Waka Kurikulum')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Waka Kurikulum</h1>
        <p class="page-subtitle">Manajemen Jadwal Piket Harian, Waka Bertugas, dan Monitoring KBM</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary" style="font-weight:600;">+ Buat Jadwal Piket</a>
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Lihat Jadwal KBM</a>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid-3 mb-24">
    <div class="stat-card" style="border-left: 4px solid #0284c7;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div>
            <div class="stat-value" style="color: #0284c7;">{{ $totalJadwal }}</div>
            <div class="stat-label">Total Hari Terjadwal</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div>
            <div class="stat-value" style="color: #d97706;">{{ count($wakas) }}</div>
            <div class="stat-label">Waka Aktif</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
        <div>
            <div class="stat-value" style="color: #16a34a;">{{ $totalPelajaran }}</div>
            <div class="stat-label">Jadwal Mapel KBM</div>
        </div>
    </div>
</div>

<!-- TODAY DUTY CARD -->
<div class="card mb-24" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color:#fff; border:none;">
    <div class="card-body" style="padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <span class="badge" style="background:rgba(255,255,255,0.2); color:#fff; font-size:11px; margin-bottom:8px;">PENUGASAN HARI INI</span>
                <h2 style="margin:0 0 6px 0; font-size:20px; color:#fff;">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </h2>
                <div style="font-size:14px; opacity:0.9;">
                    @if($jadwalHariIni)
                        Waka Bertugas: <strong>{{ $jadwalHariIni->waka->nama ?? '-' }} ({{ strtoupper(str_replace('_', ' ', $jadwalHariIni->waka->role ?? '-')) }})</strong>
                        @if($jadwalHariIni->guruPiket)
                            &bull; Guru Piket: <strong>{{ $jadwalHariIni->guruPiket->nama }}</strong>
                        @endif
                    @else
                        <span style="color:#fef08a;">⚠️ Belum ada Waka yang dijadwalkan bertugas untuk hari ini.</span>
                    @endif
                </div>
            </div>
            <div>
                @if(!$jadwalHariIni)
                    <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn" style="background:#f59e0b; color:#fff; font-weight:700;">+ Tentukan Waka Hari Ini</a>
                @else
                    <a href="{{ route('waka-kurikulum.jadwal.edit', $jadwalHariIni->id_jadwal_waka) }}" class="btn" style="background:rgba(255,255,255,0.2); color:#fff; font-weight:600;">Edit Penugasan Hari Ini</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- UPCOMING SCHEDULE TABLE -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Jadwal Penugasan Terdekat
        </h3>
        <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary btn-sm">Lihat Semua Jadwal &rarr;</a>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwalMendatang->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waka Bertugas</th>
                        <th>Guru Piket</th>
                        <th>Keterangan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($jadwalMendatang as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-' }}</td>
                        <td>
                            <strong class="text-navy">{{ $item->waka->nama ?? '-' }}</strong>
                            <div class="text-muted" style="font-size:11.5px;">{{ strtoupper(str_replace('_', ' ', $item->waka->role ?? '-')) }}</div>
                        </td>
                        <td>{{ $item->guruPiket->nama ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka-kurikulum.jadwal.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="empty-state" style="padding:30px; text-align:center;">
                <p class="text-muted">Belum ada jadwal mendatang yang dibuat.</p>
                <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary btn-sm">+ Buat Jadwal</a>
            </div>
        @endif
    </div>
</div>
@endsection