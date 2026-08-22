@extends('layouts.app')

@section('title', 'Presensi Piket — Jurnal Sekolah')
@section('page-title', 'Presensi Piket')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Presensi Piket Guru</h1>
        <p class="page-subtitle">Monitoring Kehadiran Guru Mengajar Berdasarkan Jadwal</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('piket.presensi') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label for="tanggal" class="form-label" style="margin-bottom:2px;">Pilih Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="form-control" onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter Tanggal</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Status Kehadiran Guru Berdasarkan Jadwal ({{ $tanggal }})</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($jadwalHariIni->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">Jam</th>
                        <th>Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Jadwal</th>
                        <th>Presensi Self</th>
                        <th>Status Piket</th>
                        <th class="action-col">Aksi Catat Piket</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalHariIni as $j)
                    @php
                        $piketRec = $absensiPiketList[$j->id_jadwal] ?? null;
                        $selfPresensi = $j->guru?->id_user ? ($presensiGuruMasuk[$j->guru->id_user] ?? null) : null;
                    @endphp
                    <tr>
                        <td class="no-col fw-bold">{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                        <td><span class="badge badge-navy">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? 'Belum ditentukan' }}</td>
                        <td>
                            @if($selfPresensi)
                                <span class="badge badge-success">Hadir ({{ $selfPresensi->jam_masuk }})</span>
                            @else
                                <span class="badge badge-danger">Belum Masuk</span>
                            @endif
                        </td>
                        <td>
                            @if($piketRec)
                                @php
                                    $stP = strtolower($piketRec->status_guru);
                                    $badgeP = match($stP) {
                                        'hadir' => 'badge-success',
                                        'tidak_hadir' => 'badge-danger',
                                        'terlambat' => 'badge-warning',
                                        'digantikan' => 'badge-amber',
                                        default => 'badge-gray'
                                    };
                                @endphp
                                <span class="badge {{ $badgeP }}">{{ strtoupper($piketRec->status_guru) }}</span>
                                @if($piketRec->pengganti)
                                    <div style="font-size:11px; margin-top:2px;" class="text-muted">Pengganti: {{ $piketRec->pengganti }}</div>
                                @endif
                            @else
                                <span class="badge badge-gray">Belum dicatat</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <form action="{{ route('piket.presensi-guru.store') }}" method="POST" class="d-flex align-center gap-8">
                                @csrf
                                <input type="hidden" name="id_jadwal" value="{{ $j->id_jadwal }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                
                                <select name="status_guru" class="form-control" required style="padding:4px 8px; width:auto;">
                                    <option value="hadir" {{ ($piketRec?->status_guru == 'hadir') ? 'selected' : '' }}>Hadir</option>
                                    <option value="tidak_hadir" {{ ($piketRec?->status_guru == 'tidak_hadir') ? 'selected' : '' }}>Tidak Hadir / Kosong</option>
                                    <option value="terlambat" {{ ($piketRec?->status_guru == 'terlambat') ? 'selected' : '' }}>Terlambat</option>
                                    <option value="digantikan" {{ ($piketRec?->status_guru == 'digantikan') ? 'selected' : '' }}>Digantikan</option>
                                </select>

                                <input type="text" name="pengganti" placeholder="Guru Pengganti" value="{{ $piketRec?->pengganti }}" class="form-control" style="width: 140px; padding:4px 8px;">
                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada jadwal untuk tanggal {{ $tanggal }}.</div>
        </div>
        @endif
    </div>
</div>
@endsection
