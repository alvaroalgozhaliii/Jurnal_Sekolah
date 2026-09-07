@extends('layouts.app')

@section('title', 'Siswa Terlambat — Jurnal Sekolah')
@section('page-title', 'Catatan Siswa Terlambat')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Catatan Siswa Terlambat</h1>
        <p class="page-subtitle">Data keterlambatan siswa yang dicatat petugas piket</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('piket.siswa-terlambat.create') }}" class="btn btn-primary">+ Catat Terlambat</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <div>{{ session('success') }}</div>
    @if(session('slip_id'))
        <a href="{{ route('piket.siswa-terlambat.slip', session('slip_id')) }}" target="_blank"
           class="btn btn-secondary btn-sm" style="margin-top:8px;">
            🖨️ Cetak Surat Izin Masuk Kelas
        </a>
    @endif
</div>
@endif
@if(session('error'))
<div class="alert alert-danger"><div>{{ session('error') }}</div></div>
@endif

{{-- Filter --}}
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('piket.siswa-terlambat.index') }}" method="GET" class="filter-bar">
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Filter Kelas</label>
                <select name="id_kelas" class="form-control select-search" data-searchable="true">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $idKelas == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label" style="margin-bottom:2px;">Cari Siswa</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN..." class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter</button>
            <a href="{{ route('piket.siswa-terlambat.index') }}" class="btn btn-secondary" style="align-self:flex-end;">Reset</a>
        </form>
    </div>
</div>

{{-- Stat --}}
<div class="stat-grid mb-24" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr));">
    <div class="stat-card" style="border-left:4px solid #d97706;">
        <div class="stat-icon-box amber">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <div class="stat-num" style="color:#d97706;">{{ $totalHariIni }}</div>
            <div class="stat-label">Terlambat Hari Ini</div>
        </div>
    </div>
    <div class="stat-card" style="border-left:4px solid #1e3a8a;">
        <div class="stat-icon-box blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
        </div>
        <div>
            <div class="stat-num" style="color:#1e3a8a;">{{ $terlambatList->count() }}</div>
            <div class="stat-label">Data Ditampilkan</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Keterlambatan
            @if($tanggal)
                — {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}
            @endif
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($terlambatList->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Tgl Terlambat</th>
                        <th>Terlambat s.d Jam ke-</th>
                        <th>Alasan</th>
                        <th>Tindakan Piket</th>
                        <th>Dicatat Oleh</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terlambatList as $idx => $t)
                    <tr>
                        <td class="no-col">{{ $idx + 1 }}</td>
                        <td class="fw-bold text-navy">{{ $t->siswa->nama ?? '-' }}</td>
                        <td class="text-muted">{{ $t->siswa->NISN ?? '-' }}</td>
                        <td><span class="badge badge-navy">{{ $t->kelas->nama_kelas ?? ($t->siswa->kelas->nama_kelas ?? '-') }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge" style="background:#fef3c7;color:#d97706;font-weight:700;">
                                Jam ke-{{ $t->terlambat_sampai_jam }}
                            </span>
                            @php
                                $days = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                                $hariTerlambat = $days[\Carbon\Carbon::parse($t->tanggal)->format('l')] ?? 'Senin';
                                $alokasi = \App\Services\KbmService::getAlokasiWaktu($hariTerlambat, (int)$t->terlambat_sampai_jam);
                            @endphp
                            @if($alokasi)
                                <div style="font-size:11px;color:var(--text-secondary);">{{ $alokasi['waktu_mulai'] }} – {{ $alokasi['waktu_selesai'] }}</div>
                            @endif
                        </td>
                        <td>{{ Str::limit($t->alasan, 50) }}</td>
                        <td class="text-muted">{{ Str::limit($t->tindakan_piket ?? '-', 40) }}</td>
                        <td class="text-muted" style="font-size:12px;">{{ $t->petugasPiket->nama ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('piket.siswa-terlambat.slip', $t->id_terlambat) }}" target="_blank"
                               class="btn btn-secondary btn-sm" title="Cetak Slip">🖨️</a>
                            <form action="{{ route('piket.siswa-terlambat.destroy', $t->id_terlambat) }}" method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Hapus catatan keterlambatan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada catatan siswa terlambat
                @if($tanggal) pada {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}@endif.
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
