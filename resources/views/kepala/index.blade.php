@extends('layouts.app')

@section('title', 'Persetujuan Dispen Guru — Kepala Sekolah')
@section('page-title', 'Pusat Persetujuan Dispen Guru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pusat Persetujuan Dispen Guru</h1>
        <p class="page-subtitle">Daftar dispensasi guru yang memerlukan verifikasi dan persetujuan final Kepala Sekolah</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('kepala.dashboard') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
    </div>
</div>

<!-- SECTION 1: MENUNGGU PERSETUJUAN KEPALA SEKOLAH -->
<div class="card mb-24">
    <div class="card-header" style="background:#fffbeb; border-bottom:1px solid #fef3c7;">
        <h3 class="card-title text-amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Menunggu Persetujuan Final ({{ $pendingList->count() }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pendingList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Alasan</th>
                        <th>Catatan Waka SDM</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingList as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">
                            {{ $p->guru?->nama ?? $p->pengaju?->nama ?? 'Guru' }}
                            @if($p->guru?->nip)
                                <div class="text-muted" style="font-size:11px;">NIP: {{ $p->guru->nip }}</div>
                            @endif
                        </td>
                        <td>{{ $p->tanggal }}</td>
                        <td>
                            {{ $p->jam_mulai ?? '-' }}
                            @if($p->perkiraan_kembali)
                                <span class="text-muted" style="font-size:11px;">(s/d {{ $p->perkiraan_kembali }})</span>
                            @endif
                        </td>
                        <td><strong>{{ $p->jenis_izin ?? 'Dispen Guru' }}</strong></td>
                        <td>{{ Str::limit($p->alasan, 35) }}</td>
                        <td>
                            <span class="badge badge-navy" style="font-size:11px;">Acc Waka SDM</span>
                            <div class="text-muted" style="font-size:11px;">{{ $p->catatan_waka ?? '-' }}</div>
                        </td>
                        <td class="action-col">
                            <a href="{{ route('kepala.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Beri Keputusan &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada antrean dispen guru yang menunggu persetujuan Kepala Sekolah saat ini.</div>
        </div>
        @endif
    </div>
</div>

<!-- SECTION 2: TELAH DISETUJUI -->
<div class="card mb-24">
    <div class="card-header" style="background:#f0fdf4; border-bottom:1px solid #dcfce7;">
        <h3 class="card-title text-green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Dispensasi Guru Telah Disetujui ({{ $disetujuiList->count() }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($disetujuiList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>Tanggal Dispen</th>
                        <th>Keperluan</th>
                        <th>Catatan Kepala Sekolah</th>
                        <th>Waktu Acc Kepsek</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disetujuiList as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $p->guru?->nama ?? $p->pengaju?->nama ?? 'Guru' }}</td>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ $p->jenis_izin ?? 'Dispen Guru' }}</td>
                        <td>{{ $p->catatan_kepala ?? '-' }}</td>
                        <td><span class="text-muted" style="font-size:12px;">{{ $p->tgl_kepala ? date('d M Y H:i', strtotime($p->tgl_kepala)) : '-' }}</span></td>
                        <td class="action-col">
                            <a href="{{ route('kepala.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada pengajuan dispen guru yang disetujui.</div>
        </div>
        @endif
    </div>
</div>

<!-- SECTION 3: DITOLAK -->
@if($ditolakList->count() > 0)
<div class="card">
    <div class="card-header" style="background:#fef2f2; border-bottom:1px solid #fee2e2;">
        <h3 class="card-title text-red">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Dispensasi Guru Ditolak ({{ $ditolakList->count() }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>Tanggal Dispen</th>
                        <th>Alasan Penolakan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ditolakList as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $p->guru?->nama ?? $p->pengaju?->nama ?? 'Guru' }}</td>
                        <td>{{ $p->tanggal }}</td>
                        <td><span class="text-red">{{ $p->catatan_kepala ?? 'Ditolak tanpa catatan' }}</span></td>
                        <td class="action-col">
                            <a href="{{ route('kepala.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
