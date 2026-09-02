@extends('layouts.app')

@section('title', 'Presensi Anak — Jurnal Sekolah')
@section('page-title', 'Presensi Anak')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Presensi Kehadiran Anak</h1>
        <p class="page-subtitle">Riwayat Presensi Harian Siswa</p>
    </div>
</div>

@if($anakList->count() > 1)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('ortu.presensi') }}" method="GET" class="d-flex align-center gap-12">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Anak:</label>
            <select name="id_siswa" onchange="this.form.submit()" class="form-control" style="max-width:350px;">
                @foreach($anakList as $a)
                <option value="{{ $a->id_siswa }}" {{ ($selectedSiswa && $selectedSiswa->id_siswa == $a->id_siswa) ? 'selected' : '' }}>
                    {{ $a->nama }} (NISN: {{ $a->NISN }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

@if($selectedSiswa)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Presensi: {{ $selectedSiswa->nama }} (NISN: {{ $selectedSiswa->NISN }})</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($riwayatPresensi->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Keterlambatan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($riwayatPresensi as $index => $r)
                    @php
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
                        <td class="no-col">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $r->jurnal->tanggal ?? '-' }}</td>
                        <td class="fw-bold text-navy">{{ $r->jurnal->mapel ?? '-' }}</td>
                        <td>{{ $r->jurnal->guru->nama ?? '-' }}</td>
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
            <div class="empty-state-text">Belum ada riwayat presensi.</div>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
