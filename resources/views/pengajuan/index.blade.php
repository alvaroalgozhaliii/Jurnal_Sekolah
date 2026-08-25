@extends('layouts.app')

@section('title', 'Daftar Pengajuan Dispen & Izin — Jurnal Sekolah')
@section('page-title', 'Izin & Dispensasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Daftar Pengajuan Dispensasi & Izin</h1>
        <p class="page-subtitle">Monitoring status pengajuan dispen, persetujuan Waka & verifikasi Satpam</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.create') }}" class="btn btn-primary">+ Buat Pengajuan Baru</a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($pengajuanList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Kategori</th>
                        <th>Subjek / Pemohon</th>
                        <th>Tanggal</th>
                        <th>Waka Tujuan</th>
                        <th>Waktu Keluar</th>
                        <th>Alasan</th>
                        <th>Status Saat Ini</th>
                        <th>Lampiran</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanList as $index => $p)
                    @php
                        $st = strtolower($p->status);
                        $badgeCls = match($st) {
                            'verified', 'disetujui_satpam', 'completed', 'selesai' => 'badge-success',
                            'disetujui_waka', 'menunggu_satpam', 'pending_satpam' => 'badge-info',
                            'pending_waka', 'menunggu_waka', 'pending_piket' => 'badge-warning',
                            default => 'badge-danger'
                        };
                    @endphp
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td class="fw-bold text-navy">
                            {{ $p->siswa ? $p->siswa->nama . ' (Kelas ' . ($p->siswa->kelas->nama_kelas ?? '-') . ')' : ($p->guru ? $p->guru->nama . ' (Guru)' : ($p->pengaju->nama ?? '-')) }}
                        </td>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ $p->wakaTujuan->nama ?? 'Ditentukan berdasarkan alur' }}</td>
                        <td>
                            {{ $p->jam_mulai ? $p->jam_mulai : 'Seharian' }}
                            @if($p->perkiraan_kembali)
                                <span class="text-muted" style="font-size:11px;">(Kembali: {{ $p->perkiraan_kembali }})</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($p->alasan, 35) }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper(str_replace('_', ' ', $p->status)) }}</span></td>
                        <td>
                            @if($p->lampiran_foto)
                                <a href="{{ asset('storage/' . $p->lampiran_foto) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat Foto</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('pengajuan.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Detail & Status</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada pengajuan dispensasi atau izin.</div>
        </div>
        @endif
    </div>
</div>
@endsection
