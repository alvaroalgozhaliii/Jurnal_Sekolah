@extends('layouts.app')

@section('title', 'Dashboard Waka — Jurnal Sekolah')
@section('page-title', 'Dashboard Waka')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            Dashboard Waka {{ $isSdm ? 'SDM (Izin & Dispen Guru)' : 'Kesiswaan (Dispen Siswa)' }}
        </h1>
        <p class="page-subtitle">Verifikasi, Persetujuan Dispensasi & Integrasi Notifikasi WhatsApp Gerbang</p>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid-3 mb-24">
    <div class="stat-card">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num">{{ $totalPending }}</div>
            <div class="stat-label">Pengajuan Baru (Menunggu Acc)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num">{{ $totalDisetujui }}</div>
            <div class="stat-label">Telah Disetujui</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box red">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </div>
        <div>
            <div class="stat-num">{{ $totalDitolak }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
</div>

<!-- PENDING APPROVAL WAKA -->
<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            Pending Approval Waka (Membutuhkan Keputusan Anda)
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
                            'verified', 'disetujui_waka', 'completed' => 'badge-success',
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
