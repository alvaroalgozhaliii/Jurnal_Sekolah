@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah — Jurnal Sekolah')
@section('page-title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Kepala Sekolah</h1>
        <p class="page-subtitle">Persetujuan Final Izin & Dispensasi Sekolah</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Pending Final Approval Kepala Sekolah</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($pendingKepala->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kategori</th>
                        <th>Subjek / Pemohon</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Catatan Waka</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingKepala as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td class="fw-bold text-navy">{{ $p->siswa ? $p->siswa->nama . ' (Kelas ' . ($p->siswa->kelas->nama_kelas ?? '-') . ')' : ($p->guru ? $p->guru->nama : ($p->pengaju->nama ?? '-')) }}</td>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ Str::limit($p->alasan, 30) }}</td>
                        <td class="text-muted">{{ $p->catatan_waka ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('pengajuan.show', $p->id_pengajuan) }}" class="btn btn-success btn-sm">Keputusan Final</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada antrean pengajuan yang membutuhkan keputusan Kepala Sekolah saat ini.</div>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Keputusan Kepala Sekolah</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($riwayatKepala->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Subjek</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status Akhir</th>
                        <th>Catatan Anda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatKepala as $index => $r)
                    @php
                        $st = strtolower($r->status);
                        $badgeCls = match($st) {
                            'completed' => 'badge-success',
                            default => 'badge-danger'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $r->siswa->nama ?? ($r->guru->nama ?? ($r->pengaju->nama ?? '-')) }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $r->kategori)) }}</span></td>
                        <td>{{ $r->tanggal }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $r->status)) }}</span></td>
                        <td>{{ $r->catatan_kepala ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat keputusan Kepala Sekolah.</div>
        </div>
        @endif
    </div>
</div>
@endsection
