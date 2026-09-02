@extends('layouts.app')

@section('title', 'Rekap Presensi — Jurnal Sekolah')
@section('page-title', 'Rekap Presensi Kelas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Rekap Presensi Kelas Bimbingan</h1>
        <p class="page-subtitle">Laporan bulanan kehadiran siswa kelas bimbingan</p>
    </div>
</div>

@if($kelas)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('walikelas.rekap-presensi') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Filter Siswa</label>
                <select name="id_siswa" class="form-control">
                    <option value="">-- Semua Siswa Kelas {{ $kelas->nama_kelas }} --</option>
                    @foreach($siswaList as $s)
                    <option value="{{ $s->id_siswa }}" {{ $selectedSiswaId == $s->id_siswa ? 'selected' : '' }}>
                        {{ $s->nama }} (NIS: {{ $s->nis }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Bulan</label>
                <select name="bulan" class="form-control">
                    @foreach(range(1, 12) as $m)
                    @php $monthName = DateTime::createFromFormat('!m', $m)->format('F'); @endphp
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Tahun</label>
                <select name="tahun" class="form-control">
                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter Rekap</button>
        </form>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Statistik Presensi Kelas {{ $kelas->nama_kelas }} — {{ strtoupper(DateTime::createFromFormat('!m', $bulan)->format('F')) }} {{ $tahun }}</h3>
    </div>
    <div class="card-body">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon-box green">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div>
                    <div class="stat-num">{{ $summary['hadir'] }}</div>
                    <div class="stat-label">Hadir</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box amber">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <div class="stat-num">{{ $summary['terlambat'] }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box blue">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
                <div>
                    <div class="stat-num">{{ $summary['izin'] }}</div>
                    <div class="stat-label">Izin</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box purple">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18"></path><path d="M6 6l12 12"></path></svg>
                </div>
                <div>
                    <div class="stat-num">{{ $summary['sakit'] }}</div>
                    <div class="stat-label">Sakit</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box red">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
                <div>
                    <div class="stat-num">{{ $summary['alpa'] }}</div>
                    <div class="stat-label">Alpa</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Rekap Presensi</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapData->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Keterlambatan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $r)
                    @php
                        $tgl = $r->jurnal->tanggal ?? '';
                        $hariIndo = $tgl ? \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('dddd') : '-';
                        $st = strtolower($r->status);
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
                        <td class="fw-bold">{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $hariIndo }}</td>
                        <td class="text-muted">{{ $r->siswa->nis ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $r->siswa->nama ?? '-' }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper($r->status) }}</span></td>
                        <td>{{ $r->jam_masuk ?? '-' }}</td>
                        <td>{{ $r->menit_terlambat ? $r->menit_terlambat . ' menit' : '-' }}</td>
                        <td>{{ $r->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data rekap presensi pada periode ini.</div>
        </div>
        @endif
    </div>
</div>
@else
<div class="alert alert-warning">
    <div>Anda belum ditugaskan sebagai Wali Kelas.</div>
</div>
@endif
@endsection
