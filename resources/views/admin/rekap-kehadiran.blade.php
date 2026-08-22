@extends('layouts.app')

@section('title', 'Rekap Kehadiran — Jurnal Sekolah')
@section('page-title', 'Rekap Kehadiran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Rekap Kehadiran Siswa & Guru</h1>
        <p class="page-subtitle">Laporan Presensi Harian Guru & Siswa Sekolah</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('admin.rekap-kehadiran') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Filter Kelas (Siswa)</label>
                <select name="id_kelas" class="form-control">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Filter Guru</label>
                <select name="id_guru" class="form-control">
                    <option value="">-- Semua Guru --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ $guruId == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter Rekap</button>
        </form>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Rekap Kehadiran Guru (Tanggal: {{ $tanggal }})</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapGuru->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapGuru as $index => $rg)
                    <tr>
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $rg->user->nama ?? '-' }}</td>
                        <td class="text-muted">{{ $rg->user->nip ?? '-' }}</td>
                        <td><span class="badge badge-success">{{ $rg->jam_masuk }}</span></td>
                        <td>
                            @if($rg->jam_keluar)
                                <span class="badge badge-info">{{ $rg->jam_keluar }}</span>
                            @else
                                <span class="badge badge-gray">Belum keluar</span>
                            @endif
                        </td>
                        <td>{{ $rg->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data presensi guru pada tanggal ini.</div>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rekap Kehadiran Siswa (Tanggal: {{ $tanggal }})</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($rekapSiswa->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapSiswa as $index => $rs)
                    @php
                        $st = strtolower($rs->status);
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
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="text-muted fw-bold">{{ $rs->siswa->nis ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $rs->siswa->nama ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $rs->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $rs->jurnal->mapel ?? '-' }}</td>
                        <td><span class="badge {{ $badgeCls }}">{{ strtoupper($rs->status) }}</span></td>
                        <td>{{ $rs->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada data rekap absensi siswa pada tanggal ini.</div>
        </div>
        @endif
    </div>
</div>
@endsection
