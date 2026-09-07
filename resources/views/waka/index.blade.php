@extends('layouts.app')

@section('title', 'Daftar Persetujuan Dispen — Waka')
@section('page-title', 'Persetujuan Dispen Waka')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pusat Persetujuan Dispensasi — Waka {{ $isSdm ? 'SDM' : 'Kesiswaan' }}</h1>
        <p class="page-subtitle">Kelola seluruh persetujuan dispen siswa & guru</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka.dashboard') }}" class="btn btn-secondary">&larr; Kembali ke Dashboard</a>
    </div>
</div>

<!-- TABEL PENDING PERSETUJUAN -->
<div class="card {{ $pendingList->count() > 0 ? 'card-amber' : '' }} mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Menunggu Persetujuan Waka (Pending: {{ $pendingList->count() }})
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pendingList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Subjek / Pemohon</th>
                        <th>Kelas / Info</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Jam Keluar</th>
                        <th>Alasan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingList as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">
                            {{ $p->siswa ? $p->siswa->nama : ($p->guru ? $p->guru->nama : ($p->pengaju->nama ?? '-')) }}
                        </td>
                        <td>
                            @if($p->siswa)
                                <span class="badge badge-navy">{{ $p->siswa->kelas->nama_kelas ?? '-' }}</span>
                            @else
                                <span class="badge badge-gray">Guru</span>
                            @endif
                        </td>
                        <td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td>{{ $p->tanggal }}</td>
                        <td>
                            {{ $p->jam_mulai ?? '-' }}
                            @if($p->perkiraan_kembali)
                                <span class="text-muted" style="font-size:11px;">(Kembali: {{ $p->perkiraan_kembali }})</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($p->alasan, 35) }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Beri Keputusan &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada pengajuan yang menunggu persetujuan Waka saat ini.</div>
        </div>
        @endif
    </div>
</div>

<!-- TABEL RIWAYAT DISETUJUI & DITOLAK -->
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Telah Disetujui ({{ $disetujuiList->count() }})
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($disetujuiList->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subjek</th>
                            <th>Tanggal</th>
                            <th>Status Gerbang</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($disetujuiList as $p)
                        <tr>
                            <td class="fw-bold text-navy">{{ $p->siswa->nama ?? ($p->guru->nama ?? '-') }}</td>
                            <td>{{ $p->tanggal }}</td>
                            <td>
                                @if($p->status === 'verified')
                                    <span class="badge badge-success">VALID SATPAM</span>
                                @else
                                    <span class="badge badge-info">MENUNGGU GERBANG</span>
                                @endif
                            </td>
                            <td class="action-col">
                                <a href="{{ route('waka.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Belum ada dispen yang disetujui.</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--badge-danger-text);"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                Ditolak ({{ $ditolakList->count() }})
            </h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if($ditolakList->count() > 0)
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subjek</th>
                            <th>Tanggal</th>
                            <th>Alasan Penolakan</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ditolakList as $p)
                        <tr>
                            <td class="fw-bold text-navy">{{ $p->siswa->nama ?? ($p->guru->nama ?? '-') }}</td>
                            <td>{{ $p->tanggal }}</td>
                            <td>{{ Str::limit($p->catatan_waka, 25) }}</td>
                            <td class="action-col">
                                <a href="{{ route('waka.persetujuan.show', $p->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-text">Tidak ada dispen yang ditolak.</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
