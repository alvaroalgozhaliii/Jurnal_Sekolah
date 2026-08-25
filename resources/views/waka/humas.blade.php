@extends('layouts.app')

@section('title', 'Monitoring Dinas Luar & Kemitraan — Jurnal Sekolah')
@section('page-title', 'Monitoring Hubungan Masyarakat & Kemitraan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Monitoring Dinas Luar & Kemitraan Industri</h1>
        <p class="page-subtitle">Pencatatan izin dinas luar guru, kegiatan kemitraan industri, dan kunjungan eksternal</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka.persetujuan.index') }}" class="btn btn-secondary">Persetujuan Dispen</a>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Daftar Izin Dinas Luar & Kegiatan Eksternal Guru
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($dinasLuar->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama Guru</th>
                        <th>Keperluan / Instansi Tujuan</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Status Persetujuan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($dinasLuar as $index => $item)
                    <tr>
                        <td>{{ $dinasLuar->firstItem() + $index }}</td>
                        <td class="fw-bold">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>
                        <td>
                            <strong class="text-navy">{{ $item->guru->nama ?? $item->pengaju->nama ?? 'Guru' }}</strong>
                            <div class="text-muted" style="font-size:11.5px;">{{ $item->guru->nip ? 'NIP: '.$item->guru->nip : '-' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $item->jenis_izin ?? 'Dinas Luar' }}</span>
                            <div class="text-muted" style="font-size:12px;">{{ Str::limit($item->alasan, 50) }}</div>
                        </td>
                        <td>{{ $item->jam_mulai ? $item->jam_mulai . ' s/d ' . ($item->perkiraan_kembali ?? 'Selesai') : 'Hari Penuh' }}</td>
                        <td>
                            @if(in_array($item->status, ['disetujui_kepala', 'completed', 'verified', 'selesai']))
                                <span class="badge badge-success">Disetujui Resmi</span>
                            @elseif(in_array($item->status, ['pending_waka', 'menunggu_waka']))
                                <span class="badge badge-warning">Menunggu Waka</span>
                            @elseif(in_array($item->status, ['pending_kepala']))
                                <span class="badge badge-info">Menunggu Kepsek</span>
                            @elseif(str_starts_with($item->status, 'ditolak'))
                                <span class="badge badge-danger">Ditolak</span>
                            @else
                                <span class="badge badge-secondary">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengajuan.show', $item->id_pengajuan) }}" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($dinasLuar->hasPages())
            <div style="padding:16px;">
                {{ $dinasLuar->links() }}
            </div>
        @endif

        @else
            <div class="empty-state" style="padding:40px; text-align:center;">
                <p class="text-muted">Belum ada catatan izin dinas luar guru.</p>
            </div>
        @endif
    </div>
</div>
@endsection
