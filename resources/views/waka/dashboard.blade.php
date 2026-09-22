@extends('layouts.app')

@section('title', 'Dashboard Waka — Jurnal Sekolah')
@section('page-title', 'Dashboard Waka')

@section('content')
@php
    $wakaRoleLabel = match(Auth::user()->role) {
        'waka_kesiswaan' => 'Waka Kesiswaan',
        'waka_kurikulum' => 'Waka Kurikulum',
        'waka_sdm'       => 'Waka SDM',
        'waka_sarpras'   => 'Waka Sarpras',
        'waka_humas'     => 'Waka Humas',
        default          => 'Waka'
    };
@endphp

<div class="page-header d-flex justify-between align-center flex-wrap gap-12">
    <div>
        <h1 class="page-title">
            Dashboard {{ $wakaRoleLabel }}
        </h1>
        <p class="page-subtitle">Verifikasi, Persetujuan Dispensasi &amp; Manajemen Penugasan KBM</p>
    </div>
    @if(Auth::user()->isWakaSdm() || Auth::user()->isAdmin())
        <div class="page-actions" style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('jadwal-piket.create') }}" class="btn btn-primary" style="font-weight:600;">
                + Buat Jadwal Piket
            </a>
            <a href="{{ route('jadwal-piket.index') }}" class="btn btn-secondary" style="font-weight:600;">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Kelola Jadwal Piket
            </a>
        </div>
    @endif
</div>

@include('partials.kbm-clock-banner')

<!-- CARD JADWAL PIKET HARI INI (WAKA SDM) -->
@if(Auth::user()->isWakaSdm() || Auth::user()->isAdmin())
    <div class="card mb-24" style="border-left: 4px solid var(--navy-primary); overflow:hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, rgba(30, 58, 138, 0.05), rgba(59, 130, 246, 0.03)); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:12px 20px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:8px; background:var(--navy-primary); color:#fff; display:flex; align-items:center; justify-content:center;">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <div>
                    <h3 class="card-title" style="margin:0; font-size:15px; font-weight:700;">Penugasan Piket Hari Ini</h3>
                    <p style="margin:2px 0 0; font-size:11.5px; color:var(--text-muted);">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('jadwal-piket.template-csv') }}" class="btn btn-secondary btn-sm" style="font-size:11.5px;">Template CSV</a>
                <a href="{{ route('jadwal-piket.index') }}" class="btn btn-secondary btn-sm" style="font-size:11.5px;">Lihat Kalender Piket &rarr;</a>
            </div>
        </div>
        <div class="card-body" style="padding:14px 20px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px;">
                <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
                    <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:3px;">Waka Bertugas</span>
                    @if(isset($piketHariIni) && $piketHariIni && $piketHariIni->waka)
                        <strong class="text-navy" style="font-size:14px;">{{ $piketHariIni->waka->nama }}</strong>
                        <div class="text-muted" style="font-size:11.5px; margin-top:2px;">
                            {{ strtoupper(str_replace('_', ' ', $piketHariIni->waka->role)) }}
                            @if($piketHariIni->waka->no_hp)
                                &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $piketHariIni->waka->no_hp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $piketHariIni->waka->no_hp }}</a>
                            @endif
                        </div>
                    @else
                        <span class="text-muted" style="font-size:13px; font-style:italic;">Belum ditentukan</span>
                    @endif
                </div>

                <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
                    <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:3px;">Guru Piket</span>
                    @if(isset($piketHariIni) && $piketHariIni && $piketHariIni->guruPiket)
                        <strong style="font-size:14px; color:var(--text-primary);">{{ $piketHariIni->guruPiket->nama }}</strong>
                        <div class="text-muted" style="font-size:11.5px; margin-top:2px;">
                            {{ $piketHariIni->guruPiket->bidang_studi ?? 'Guru Piket' }}
                            @if($piketHariIni->guruPiket->no_telp)
                                &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $piketHariIni->guruPiket->no_telp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $piketHariIni->guruPiket->no_telp }}</a>
                            @endif
                        </div>
                    @else
                        <span class="text-muted" style="font-size:13px; font-style:italic;">Belum ditentukan</span>
                    @endif
                </div>

                @if(isset($piketHariIni) && $piketHariIni && $piketHariIni->keterangan)
                    <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:10px 14px;">
                        <span class="text-muted" style="font-size:11px; font-weight:700; text-transform:uppercase; display:block; margin-bottom:3px;">Keterangan</span>
                        <div style="font-size:12.5px; color:var(--text-primary);">{{ $piketHariIni->keterangan }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- STAT CARDS -->
<div class="grid-3 mb-24">
    <div class="stat-card" style="border-left: 4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #d97706;">{{ $totalPending }}</div>
            <div class="stat-label">Pengajuan Baru (Menunggu Acc)</div>
        </div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #16a34a;">{{ $totalDisetujui }}</div>
            <div class="stat-label">Telah Disetujui</div>
        </div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc2626;">
        <div class="stat-icon-box red">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #dc2626;">{{ $totalDitolak }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
</div>

<!-- PENDING APPROVAL WAKA -->
<div class="card {{ $pengajuanPending->count() > 0 ? 'card-amber' : '' }} mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon {{ $pengajuanPending->count() > 0 ? '' : 'text-navy' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Menunggu Persetujuan {{ $wakaRoleLabel }} (Pending: {{ $pengajuanPending->count() }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pengajuanPending->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kategori</th>
                        <th>Subjek / Pemohon</th>
                        <th>Tanggal</th>
                        <th>Jam Keluar</th>
                        <th>Alasan Lengkap</th>
                        <th>Pengaju</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanPending as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td class="fw-bold text-navy">
                            {{ $p->siswa ? $p->siswa->nama . ' (Kelas ' . ($p->siswa->kelas->nama_kelas ?? '-') . ')' : ($p->guru ? $p->guru->nama : ($p->pengaju->nama ?? '-')) }}
                        </td>
                        <td>{{ $p->tanggal }}</td>
                        <td>
                            {{ $p->jam_mulai ?? '-' }}
                            @if($p->perkiraan_kembali)
                                <span class="text-muted" style="font-size:11px;">(Kembali: {{ $p->perkiraan_kembali }})</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($p->alasan, 35) }}</td>
                        <td>{{ $p->pengaju->nama ?? '-' }} ({{ strtoupper($p->pengaju->role ?? '-') }})</td>
                        <td class="action-col">
                            <a href="{{ route('waka.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Buka & Beri Keputusan &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada antrean dispen yang membutuhkan persetujuan Waka saat ini.</div>
        </div>
        @endif
    </div>
</div>

<!-- RIWAYAT KEPUTUSAN WAKA -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            Riwayat Keputusan Waka
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pengajuanRiwayat->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Subjek</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status Akhir</th>
                        <th>Catatan Waka</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanRiwayat as $index => $r)
                    @php
                        $st = strtolower($r->status);
                        $badgeCls = match($st) {
                            'verified', 'disetujui_waka', 'completed', 'selesai' => 'badge-success',
                            default => 'badge-danger'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $r->siswa->nama ?? ($r->guru->nama ?? ($r->pengaju->nama ?? '-')) }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $r->kategori)) }}</span></td>
                        <td>{{ $r->tanggal }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $r->status)) }}</span></td>
                        <td>{{ $r->catatan_waka ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka.persetujuan.show', $r->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat keputusan Waka.</div>
        </div>
        @endif
    </div>
</div>
@endsection
