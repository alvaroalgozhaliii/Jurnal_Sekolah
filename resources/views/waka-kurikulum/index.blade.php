@extends('layouts.app')

@section('title', 'Jadwal Piket & Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Piket & Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Piket &amp; Waka Bertugas</h1>
        <p class="page-subtitle">Manajemen penugasan Waka Piket dan Guru Piket — tampil per bulan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary" style="font-weight:600;">+ Buat Jadwal Bulanan</a>
    </div>
</div>

{{-- Filter Bulan & Tahun --}}
<div class="card mb-16" style="padding:16px 20px;">
    <form method="GET" action="{{ route('waka-kurikulum.index') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div class="form-group" style="margin:0;">
            <label class="form-label" style="margin-bottom:4px;">Bulan</label>
            <select class="form-control" name="bulan" style="min-width:140px;">
                @php $bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                @foreach($bulanNama as $i => $nama)
                    <option value="{{ $i+1 }}" @selected($bulan == $i+1)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label" style="margin-bottom:4px;">Tahun</label>
            <select class="form-control" name="tahun" style="min-width:100px;">
                @for($y = date('Y')-1; $y <= date('Y')+2; $y++)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Tampilkan</button>

        @if($bulanTersedia->count() > 0)
            <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span class="text-muted" style="font-size:12px;">Bulan tersedia:</span>
                @foreach($bulanTersedia as $bt)
                    <a href="{{ route('waka-kurikulum.index', ['bulan' => $bt->bulan, 'tahun' => $bt->tahun]) }}"
                       class="badge"
                       style="background: {{ ($bt->bulan == $bulan && $bt->tahun == $tahun) ? 'var(--navy-primary)' : '#e2e8f0' }}; color: {{ ($bt->bulan == $bulan && $bt->tahun == $tahun) ? '#fff' : '#334155' }}; text-decoration:none; padding:4px 8px; font-size:11px;">
                        {{ $bulanNama[$bt->bulan - 1] }} {{ $bt->tahun }}
                    </a>
                @endforeach
            </div>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Jadwal Bulan <strong>{{ $bulanNama[$bulan-1] }} {{ $tahun }}</strong>
        </h3>
        <span class="badge" style="background:var(--navy-primary); color:#fff; font-size:11px;">
            {{ $jadwal->total() }} hari terdaftar
        </span>
    </div>
    <div class="card-body" style="padding:0;">
        @if(session('success'))
            <div class="alert alert-success" style="margin:16px;">{{ session('success') }}</div>
        @endif

        @if($jadwal->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Waka yang Bertugas</th>
                        <th>Guru Piket</th>
                        <th>Keterangan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($jadwal as $index => $item)
                    @php
                        $isToday   = $item->tanggal ? $item->tanggal->isToday() : false;
                        $isWeekend = $item->tanggal ? in_array($item->tanggal->dayOfWeek, [0, 6]) : false;
                    @endphp
                    <tr style="{{ $isToday ? 'background:rgba(22,163,74,0.07);' : ($isWeekend ? 'background:rgba(245,158,11,0.05);' : '') }}">
                        <td>{{ $jadwal->firstItem() + $index }}</td>
                        <td class="fw-bold">
                            {{ $item->tanggal ? $item->tanggal->format('d') : '-' }}
                            @if($isToday)
                                <span class="badge" style="background:#16a34a; color:#fff; font-size:10px; margin-left:4px;">HARI INI</span>
                            @endif
                        </td>
                        <td style="color:{{ $isWeekend ? '#d97706' : 'inherit' }}; font-weight:{{ $isWeekend ? '700' : '500' }};">
                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') : '-' }}
                        </td>
                        <td>
                            <strong class="text-navy">{{ $item->waka->nama ?? '-' }}</strong>
                            <div class="text-muted" style="font-size:11.5px;">
                                {{ strtoupper(str_replace('_', ' ', $item->waka->role ?? '-')) }}
                                @if($item->waka && $item->waka->no_hp)
                                    &bull; 📱 {{ $item->waka->no_hp }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->guruPiket)
                                <span class="fw-bold">{{ $item->guruPiket->nama }}</span>
                                <div class="text-muted" style="font-size:11.5px;">{{ $item->guruPiket->bidang_studi ?? 'Guru Piket' }}</div>
                            @else
                                <span class="text-muted"><em>Belum ditentukan</em></span>
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka-kurikulum.jadwal.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="{{ route('waka-kurikulum.jadwal.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penugasan tanggal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($jadwal->hasPages())
            <div style="padding:16px;">{{ $jadwal->links() }}</div>
        @endif

        @else
            <div class="empty-state" style="padding:40px 20px; text-align:center;">
                <p class="text-muted">Belum ada jadwal piket untuk <strong>{{ $bulanNama[$bulan-1] }} {{ $tahun }}</strong>.</p>
                <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary btn-sm">+ Buat Jadwal Bulanan</a>
            </div>
        @endif
    </div>
</div>
@endsection
