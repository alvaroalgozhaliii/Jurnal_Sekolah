@extends('layouts.app')

@section('title', 'Presensi Saya — Jurnal Sekolah')
@section('page-title', 'Presensi Saya')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Presensi Saya (Guru)</h1>
        <p class="page-subtitle">Catat kehadiran masuk & keluar mengajar harian</p>
    </div>
</div>

<div class="card mb-24" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Presensi Hari Ini ({{ date('d/m/Y') }})</h3>
    </div>
    <div class="card-body">
        @if(!$presensiHariIni)
            <div class="alert alert-warning mb-16">
                <div>Anda belum melakukan presensi masuk hari ini.</div>
            </div>
            <form action="{{ route('guru.presensi-masuk') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="keterangan">Keterangan (Opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Hadir Tepat Waktu" class="form-control">
                </div>
                <button type="submit" class="btn btn-success btn-lg">
                    PRESENSI MASUK SEKARANG
                </button>
            </form>
        @else
            <div class="grid-2 mb-16">
                <div class="stat-card">
                    <div class="stat-icon-box green">
                        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </div>
                    <div>
                        <div class="stat-num" style="font-size:20px;">{{ $presensiHariIni->jam_masuk }}</div>
                        <div class="stat-label">Jam Masuk</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon-box {{ $presensiHariIni->jam_keluar ? 'blue' : 'amber' }}">
                        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div class="stat-num" style="font-size:20px;">{{ $presensiHariIni->jam_keluar ?? 'Belum' }}</div>
                        <div class="stat-label">Jam Keluar</div>
                    </div>
                </div>
            </div>

            @if(!$presensiHariIni->jam_keluar)
                <form action="{{ route('guru.presensi-keluar') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg">
                        PRESENSI KELUAR
                    </button>
                </form>
            @else
                <div class="alert alert-success">
                    <div>Anda telah menyelesaikan presensi masuk dan keluar hari ini.</div>
                </div>
            @endif
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Presensi Saya</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($riwayatPresensi->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatPresensi as $index => $rp)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $rp->tanggal }}</td>
                        <td><span class="badge badge-success">{{ $rp->jam_masuk }}</span></td>
                        <td>
                            @if($rp->jam_keluar)
                                <span class="badge badge-info">{{ $rp->jam_keluar }}</span>
                            @else
                                <span class="badge badge-gray">Belum keluar</span>
                            @endif
                        </td>
                        <td>{{ $rp->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada riwayat presensi.</div>
        </div>
        @endif
    </div>
</div>
@endsection
