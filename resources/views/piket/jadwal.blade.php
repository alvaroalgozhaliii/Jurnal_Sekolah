@extends('layouts.app')

@section('title', 'Jadwal Piket Sekolah — Jurnal Sekolah')
@section('page-title', 'Jadwal Piket Sekolah')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Piket Sekolah</h1>
        <p class="page-subtitle">Pantau penugasan Waka Bertugas dan Guru Piket setiap hari</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal-piket.export-csv', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary btn-sm" title="Export jadwal ke CSV">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export CSV
        </a>
    </div>
</div>

{{-- Banner Petugas Piket Hari Ini --}}
@if($piketHariIni)
    <div class="card mb-16" style="border-left: 4px solid var(--navy-primary); background: linear-gradient(135deg, rgba(30, 58, 138, 0.04) 0%, rgba(255, 255, 255, 0.9) 100%);">
        <div class="card-body" style="padding: 18px 24px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <span class="badge" style="background:#16a34a; color:#fff; font-size:11px; padding:3px 8px; font-weight:700;">HARI INI</span>
                <span class="text-muted" style="font-size:13px; font-weight:600;">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>
            <h3 style="margin:0 0 12px 0; font-size:18px; color:var(--text-primary);">
                Petugas Piket &amp; Waka Bertugas Hari Ini
            </h3>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px; margin-top:8px;">
                <div style="background:#fff; border:1px solid var(--border, #e2e8f0); border-radius:8px; padding:12px 16px;">
                    <div class="text-muted" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:4px;">
                        Waka Bertugas
                    </div>
                    <div class="fw-bold text-navy" style="font-size:15px;">
                        {{ $piketHariIni->waka->nama ?? '-' }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-top:2px;">
                        {{ strtoupper(str_replace('_', ' ', $piketHariIni->waka->role ?? '-')) }}
                        @if($piketHariIni->waka && $piketHariIni->waka->no_hp)
                            &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $piketHariIni->waka->no_hp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $piketHariIni->waka->no_hp }}</a>
                        @endif
                    </div>
                </div>

                <div style="background:#fff; border:1px solid var(--border, #e2e8f0); border-radius:8px; padding:12px 16px;">
                    <div class="text-muted" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:4px;">
                        Guru Piket
                    </div>
                    @if($piketHariIni->guruPiket)
                        <div class="fw-bold" style="font-size:15px; color:#1e293b;">
                            {{ $piketHariIni->guruPiket->nama }}
                        </div>
                        <div class="text-muted" style="font-size:12px; margin-top:2px;">
                            {{ $piketHariIni->guruPiket->bidang_studi ?? 'Guru Piket' }}
                            @if($piketHariIni->guruPiket->no_telp)
                                &bull; <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $piketHariIni->guruPiket->no_telp) }}" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600;">WA: {{ $piketHariIni->guruPiket->no_telp }}</a>
                            @endif
                        </div>
                    @else
                        <div class="text-muted" style="font-size:13px; font-style:italic;">Belum ditentukan</div>
                    @endif
                </div>

                @if($piketHariIni->keterangan)
                <div style="background:#fff; border:1px solid var(--border, #e2e8f0); border-radius:8px; padding:12px 16px;">
                    <div class="text-muted" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:4px;">
                        Keterangan / Catatan
                    </div>
                    <div style="font-size:13px; color:#334155;">
                        {{ $piketHariIni->keterangan }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Filter Periode Bulan & Tahun --}}
<div class="card mb-16" style="padding:16px 20px;">
    <form method="GET" action="{{ route('piket.jadwal-piket') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
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
        <div class="form-group" style="margin:0; flex:1; min-width:180px;">
            <label class="form-label" style="margin-bottom:4px;">Cari Guru / Waka</label>
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau keterangan...">
        </div>
        <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Tampilkan</button>
        @if(request('q'))
            <a href="{{ route('piket.jadwal-piket', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary" style="padding:8px 14px;">Reset</a>
        @endif

        @if($bulanTersedia->count() > 0)
            <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span class="text-muted" style="font-size:12px;">Bulan tersedia:</span>
                @foreach($bulanTersedia->take(6) as $bt)
                    <a href="{{ route('piket.jadwal-piket', ['bulan' => $bt->bulan, 'tahun' => $bt->tahun]) }}"
                       class="badge"
                       style="background: {{ ($bt->bulan == $bulan && $bt->tahun == $tahun) ? 'var(--navy-primary)' : '#e2e8f0' }}; color: {{ ($bt->bulan == $bulan && $bt->tahun == $tahun) ? '#fff' : '#334155' }}; text-decoration:none; padding:4px 8px; font-size:11px;">
                        {{ $bulanNama[$bt->bulan - 1] }} {{ $bt->tahun }}
                    </a>
                @endforeach
            </div>
        @endif
    </form>
</div>

{{-- Tabel Jadwal Piket Bulanan --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Jadwal Piket Bulan <strong>{{ $bulanNama[$bulan-1] }} {{ $tahun }}</strong>
            @if(request('q'))
                <span class="text-muted" style="font-size:13px; font-weight:normal;">(Filter: "{{ request('q') }}")</span>
            @endif
        </h3>
        <span class="badge" style="background:var(--navy-primary); color:#fff; font-size:11px;">
            {{ $jadwal->total() }} hari terjadwal
        </span>
    </div>
    <div class="card-body" style="padding:0;">
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
                    </tr>
                </thead>
                <tbody>
                @foreach($jadwal as $index => $item)
                    @php
                        $isToday   = $item->tanggal ? $item->tanggal->isToday() : false;
                        $isWeekend = $item->tanggal ? in_array($item->tanggal->dayOfWeek, [0, 6]) : false;
                    @endphp
                    <tr style="{{ $isToday ? 'background:rgba(22,163,74,0.07);' : ($isWeekend ? 'background:rgba(245,158,11,0.04);' : '') }}">
                        <td>{{ $jadwal->firstItem() + $index }}</td>
                        <td class="fw-bold">
                            {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
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
                                    &bull; {{ $item->waka->no_hp }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->guruPiket)
                                <span class="fw-bold">{{ $item->guruPiket->nama }}</span>
                                <div class="text-muted" style="font-size:11.5px;">
                                    {{ $item->guruPiket->bidang_studi ?? 'Guru Piket' }}
                                    @if($item->guruPiket->no_telp)
                                        &bull; {{ $item->guruPiket->no_telp }}
                                    @endif
                                </div>
                            @else
                                <span class="text-muted"><em>Belum ditentukan</em></span>
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
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
            </div>
        @endif
    </div>
</div>
@endsection
