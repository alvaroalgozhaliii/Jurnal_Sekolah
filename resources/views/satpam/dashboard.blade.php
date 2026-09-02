@extends('layouts.app')

@section('title', 'Dashboard Satpam — Jurnal Sekolah')
@section('page-title', 'Dashboard Satpam')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Petugas Keamanan (Satpam)</h1>
        <p class="page-subtitle">Verifikasi Fisik Kartu Tanda Pelajar & Izin Gerbang Sekolah (Acc Waka)</p>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid-2 mb-24">
    <div class="stat-card" style="border-left: 4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #d97706;">{{ $antreanVerifikasi->count() }}</div>
            <div class="stat-label">Menunggu Verifikasi Gerbang</div>
        </div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <div class="stat-icon-box green">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color: #16a34a;">{{ $riwayatVerifikasi->where('status', 'verified')->count() }}</div>
            <div class="stat-label">Izin Terverifikasi (Valid)</div>
        </div>
    </div>
</div>

<!-- ANTREAN MENUNGGU VERIFIKASI -->
<div class="card {{ $antreanVerifikasi->count() > 0 ? 'card-amber' : '' }} mb-24">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Antrean Verifikasi Kartu Pelajar & Izin Gerbang (Telah Disetujui Waka)
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($antreanVerifikasi->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Subjek / Nama</th>
                        <th>Kelas / Info</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Jam Keluar</th>
                        <th>Waka Approver</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($antreanVerifikasi as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $p->siswa ? $p->siswa->nama : ($p->guru ? $p->guru->nama : ($p->pengaju->nama ?? '-')) }}</td>
                        <td>
                            @if($p->siswa)
                                <span class="badge badge-navy">{{ $p->siswa->kelas->nama_kelas ?? '-' }}</span> (NIS: {{ $p->siswa->nis }})
                            @else
                                <span class="badge badge-gray">Guru / Karyawan</span>
                            @endif
                        </td>
                        <td><span class="badge badge-purple">{{ strtoupper(str_replace('_', ' ', $p->kategori)) }}</span></td>
                        <td>{{ $p->tanggal }}</td>
                        <td>
                            {{ $p->jam_mulai ?? 'Hari ini' }}
                            @if($p->perkiraan_kembali)
                                <span class="text-muted" style="font-size:11px;">(Kembali: {{ $p->perkiraan_kembali }})</span>
                            @endif
                        </td>
                        <td><span class="badge badge-success">{{ $p->wakaApprover->nama ?? 'Waka' }}</span></td>
                        <td class="action-col">
                            <a href="{{ route('satpam.show', $p->id_pengajuan) }}" class="btn btn-primary btn-sm">Periksa Kartu Pelajar &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada antrean dispen yang membutuhkan verifikasi gerbang saat ini.</div>
        </div>
        @endif
    </div>
</div>

<!-- RIWAYAT VERIFIKASI TERAKHIR -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            Riwayat Verifikasi Gerbang
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($riwayatVerifikasi->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Subjek / Nama</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Hasil Verifikasi</th>
                        <th>Waktu Verifikasi</th>
                        <th>Catatan Satpam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatVerifikasi as $index => $p)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $p->siswa ? $p->siswa->nama : ($p->guru ? $p->guru->nama : ($p->pengaju->nama ?? '-')) }}</td>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ $p->jam_mulai ?? '-' }}</td>
                        <td>
                            @if($p->status_satpam === 'valid' || $p->status === 'verified')
                                <span class="badge badge-success">VALID</span>
                            @else
                                <span class="badge badge-danger">TIDAK VALID</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $p->tgl_satpam ?? '-' }}</td>
                        <td>{{ $p->catatan_satpam ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat verifikasi.</div>
        </div>
        @endif
    </div>
</div>
@endsection
