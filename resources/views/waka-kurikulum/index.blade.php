@extends('layouts.app')

@section('title', 'Jadwal Piket & Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Piket & Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Piket &amp; Waka Bertugas</h1>
        <p class="page-subtitle">Manajemen penugasan Waka Piket dan Koordinator Piket KBM — tampil per bulan</p>
    </div>
    <div class="page-actions" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('jadwal-piket.template-csv') }}" class="btn btn-secondary btn-sm" title="Download template format CSV">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Template CSV
        </a>
        <a href="{{ route('jadwal-piket.export-csv', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary btn-sm" title="Export jadwal bulan ini ke CSV">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Export CSV
        </a>
        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modalImportCsv').style.display='flex';" style="font-weight:600;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Upload CSV
        </button>
        <a href="{{ route('jadwal-piket.create') }}" class="btn btn-primary" style="font-weight:600;">
            + Buat Jadwal Bulanan
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-16">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-16">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Filter Bulan & Tahun & Pencarian --}}
<div class="card mb-16" style="padding:16px 20px;">
    <form method="GET" action="{{ route('jadwal-piket.index') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
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
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Ketik nama koordinator, petugas, waka, atau catatan...">
        </div>
        <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Tampilkan</button>
        @if(request('q'))
            <a href="{{ route('jadwal-piket.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary" style="padding:8px 14px;">Reset</a>
        @endif

        @if($bulanTersedia->count() > 0)
            <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span class="text-muted" style="font-size:12px;">Bulan tersedia:</span>
                @foreach($bulanTersedia->take(6) as $bt)
                    <a href="{{ route('jadwal-piket.index', ['bulan' => $bt->bulan, 'tahun' => $bt->tahun]) }}"
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
            @if(request('q'))
                <span class="text-muted" style="font-size:13px; font-weight:normal;">(Filter: "{{ request('q') }}")</span>
            @endif
        </h3>
        <span class="badge" style="background:var(--navy-primary); color:#fff; font-size:11px;">
            {{ $jadwal->total() }} hari terdaftar
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
                        <th>Koordinator Piket (Pagi)</th>
                        <th>Keterangan</th>
                        <th class="action-col" style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($jadwal as $index => $item)
                    @php
                        $isToday   = $item->tanggal ? $item->tanggal->isToday() : false;
                        $isWeekend = $item->tanggal ? in_array($item->tanggal->dayOfWeek, [0, 6]) : false;
                        $koorPagi  = $item->koordinator_pagi ?: ($item->guruPiket ? $item->guruPiket->nama : null);
                        $koorSiang = $item->koordinator_siang ?: null;
                    @endphp
                    <tr style="{{ $isToday ? 'background:rgba(22,163,74,0.07);' : ($isWeekend ? 'background:rgba(245,158,11,0.05);' : '') }}">
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
                            @if($koorPagi)
                                <span class="fw-bold">{{ $koorPagi }}</span>
                                <div class="text-muted" style="font-size:11.5px;">
                                    <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:10px; padding:1px 5px; font-weight:600;">Sesi Pagi 07.00 - 11.00</span>
                                </div>
                            @else
                                <span class="text-muted"><em>Belum ditentukan</em></span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:12.5px;">{{ $item->keterangan ?? '-' }}</span>
                        </td>
                        <td class="action-col" style="text-align:center;">
                            <div style="display:inline-flex; gap:5px; align-items:center;">
                                <button type="button" 
                                        class="btn btn-secondary btn-sm" 
                                        onclick="openPiketDetail(this)"
                                        data-tanggal="{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}"
                                        data-hari="{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}"
                                        data-is-today="{{ $isToday ? '1' : '0' }}"
                                        data-waka="{{ $item->waka->nama ?? '-' }}"
                                        data-waka-role="{{ $item->waka ? strtoupper(str_replace('_', ' ', $item->waka->role)) : '-' }}"
                                        data-waka-hp="{{ $item->waka->no_hp ?? '' }}"
                                        data-koor-pagi="{{ $koorPagi ?? '-' }}"
                                        data-petugas-pagi="{{ $item->petugas_pagi ?? '' }}"
                                        data-koor-siang="{{ $koorSiang ?? '-' }}"
                                        data-petugas-siang="{{ $item->petugas_siang ?? '' }}"
                                        data-keterangan="{{ $item->keterangan ?? '' }}"
                                        style="font-size:11.5px; padding:4px 8px; font-weight:600; display:inline-flex; align-items:center; gap:4px;"
                                        title="View seluruh data piket">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    View
                                </button>
                                <a href="{{ route('jadwal-piket.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm" style="font-size:11.5px; padding:4px 8px;">Edit</a>
                                <form action="{{ route('jadwal-piket.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penugasan tanggal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="font-size:11.5px; padding:4px 8px;">Hapus</button>
                                </form>
                            </div>
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
                <div style="display:flex; gap:10px; justify-content:center; margin-top:12px;">
                    <a href="{{ route('jadwal-piket.create') }}" class="btn btn-primary btn-sm">+ Buat Jadwal Bulanan</a>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modalImportCsv').style.display='flex';">Upload CSV</button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL SELURUH PETUGAS PIKET HARIAN --}}
<div id="modalDetailPiket" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.65); backdrop-filter:blur(3px); z-index:9999; align-items:center; justify-content:center; padding:16px; box-sizing:border-box;">
    <div class="card" style="width:100%; max-width:620px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); border-radius:14px; overflow:hidden; animation:modalPopIn 0.2s ease-out;">
        
        {{-- Header Modal --}}
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:flex-start; padding:16px 20px; border-bottom:1px solid var(--border, #e2e8f0); background:var(--bg-card-header, #f8fafc);">
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                    <span id="modalPiketBadgeToday" class="badge" style="background:#16a34a; color:#fff; font-size:10.5px; font-weight:700; display:none;">HARI INI</span>
                    <h3 class="card-title" style="margin:0; font-size:16px; display:inline-flex; align-items:center; gap:8px;">
                        <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Detail Petugas Piket KBM
                    </h3>
                </div>
                <p id="modalPiketTanggalText" class="text-muted" style="margin:0; font-size:13px; font-weight:600; color:var(--navy-primary, #1e3a8a);">-</p>
            </div>
            <button type="button" onclick="closePiketDetail()" style="background:none; border:none; font-size:22px; line-height:1; cursor:pointer; color:#64748b; padding:4px 8px; border-radius:6px;" title="Tutup modal">&times;</button>
        </div>

        {{-- Body Modal --}}
        <div class="card-body" style="padding:18px 20px; overflow-y:auto; flex:1; display:flex; flex-direction:column; gap:14px;">
            
            {{-- Waka Bertugas Card --}}
            <div style="background:var(--bg-page, #f8fafc); border:1px solid var(--border, #e2e8f0); border-left:4px solid var(--navy-primary, #1e3a8a); border-radius:8px; padding:12px 16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <span class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700;">PIKET WAKA</span>
                    <span id="modalPiketWakaRole" class="badge" style="background:#e0e7ff; color:#3730a3; font-size:10.5px; font-weight:600;">-</span>
                </div>
                <div id="modalPiketWakaNama" class="fw-bold text-navy" style="font-size:15px;">-</div>
                <div id="modalPiketWakaHpBox" style="font-size:12px; margin-top:4px; display:none;">
                    <a id="modalPiketWakaHpLink" href="#" target="_blank" style="color:#16a34a; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                        <span>WA:</span> <span id="modalPiketWakaHpVal">-</span>
                    </a>
                </div>
            </div>

            {{-- Grid Sesi Pagi & Sesi Siang --}}
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
                
                {{-- Sesi Pagi (07.00 - 11.00) --}}
                <div style="background:#ffffff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 16px; box-shadow:0 2px 6px rgba(59,130,246,0.04);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; padding-bottom:6px; border-bottom:1px dashed #e2e8f0;">
                        <span style="font-weight:700; font-size:13px; color:#0369a1; display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                            SESI PAGI
                        </span>
                        <span class="badge" style="background:#0284c7; color:#fff; font-size:10px; font-weight:700; padding:2px 7px;">07.00 - 11.00</span>
                    </div>

                    <div style="margin-bottom:10px;">
                        <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:2px;">
                            Koordinator Piket Pagi
                        </div>
                        <div id="modalPiketKoorPagi" style="font-size:13.5px; font-weight:700; color:#0f172a;">-</div>
                    </div>

                    <div>
                        <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:6px;">
                            Petugas Piket Pagi
                        </div>
                        <div id="modalPiketPetugasPagiList">-</div>
                    </div>
                </div>

                {{-- Sesi Siang (11.00 - 15.00) --}}
                <div style="background:#ffffff; border:1px solid #fed7aa; border-radius:10px; padding:14px 16px; box-shadow:0 2px 6px rgba(249,115,22,0.04);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; padding-bottom:6px; border-bottom:1px dashed #e2e8f0;">
                        <span style="font-weight:700; font-size:13px; color:#c2410c; display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            SESI SIANG
                        </span>
                        <span class="badge" style="background:#ea580c; color:#fff; font-size:10px; font-weight:700; padding:2px 7px;">11.00 - 15.00</span>
                    </div>

                    <div style="margin-bottom:10px;">
                        <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:2px;">
                            Koordinator Piket Siang
                        </div>
                        <div id="modalPiketKoorSiang" style="font-size:13.5px; font-weight:700; color:#0f172a;">-</div>
                    </div>

                    <div>
                        <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:6px;">
                            Petugas Piket Siang
                        </div>
                        <div id="modalPiketPetugasSiangList">-</div>
                    </div>
                </div>

            </div>

            {{-- Keterangan / Catatan Card --}}
            <div id="modalPiketKetBox" style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:10px 14px; display:none;">
                <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; margin-bottom:2px;">
                    Catatan / Keterangan Khusus
                </div>
                <div id="modalPiketKetText" style="font-size:12.5px; color:#334155;">-</div>
            </div>

        </div>

        {{-- Footer Modal --}}
        <div class="card-footer" style="padding:12px 20px; background:var(--bg-card-header, #f8fafc); border-top:1px solid var(--border, #e2e8f0); display:flex; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closePiketDetail()" style="padding:6px 16px; font-weight:600;">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- MODAL UPLOAD / IMPORT CSV --}}
<div id="modalImportCsv" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="card" style="width:90%; max-width:520px; box-shadow:0 20px 40px rgba(0,0,0,0.3); border-radius:12px;">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="card-title" style="margin:0;">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Upload / Import CSV Jadwal Piket
            </h3>
            <button type="button" onclick="document.getElementById('modalImportCsv').style.display='none';" style="background:none; border:none; font-size:20px; cursor:pointer; color:#64748b;">&times;</button>
        </div>
        <form action="{{ route('jadwal-piket.import-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body" style="padding:20px;">
                <p class="text-muted" style="font-size:13px; margin-bottom:14px;">
                    Unggah file format <strong>.csv</strong> untuk menambahkan atau memperbarui jadwal piket harian. Mendukung format tabel jadwal lengkap (Petugas Pagi, Koordinator Pagi, Petugas Siang, Koordinator Siang, Piket Waka).
                </p>

                <div style="background:var(--bg-card-header, #f8fafc); border:1px dashed var(--border, #cbd5e1); border-radius:8px; padding:20px; text-align:center; margin-bottom:16px;">
                    <svg class="svg-icon text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:36px;height:36px; margin-bottom:8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <div style="margin-bottom:8px;">
                        <input type="file" name="csv_file" id="csv_file_input" accept=".csv,text/csv" required style="font-size:13px;">
                    </div>
                    <span class="text-muted" style="font-size:11.5px;">Maksimal ukuran file: 10 MB</span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:var(--badge-info-bg, #eff6ff); border:1px solid rgba(14,165,233,0.2); border-radius:6px;">
                    <span style="font-size:12px; color:var(--text-primary);">Belum punya formatnya?</span>
                    <a href="{{ route('jadwal-piket.template-csv') }}" class="btn btn-secondary btn-sm" style="font-size:11px; padding:4px 10px;">
                        Download Template CSV
                    </a>
                </div>
            </div>
            <div class="card-footer" style="display:flex; justify-content:flex-end; gap:10px; padding:12px 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalImportCsv').style.display='none';">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight:600;">Unggah &amp; Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalPopIn {
    0% { transform: scale(0.96); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderTeacherList(rawText) {
    if (!rawText || !rawText.trim() || rawText.trim() === '-') {
        return '<div class="text-muted" style="font-style:italic; font-size:12px; padding:4px 0;">Tidak ada daftar petugas tambahan</div>';
    }
    let items = rawText.split(/[\r\n;]+/).map(s => s.trim()).filter(Boolean);
    if (items.length === 1 && items[0].includes(' / ')) {
        items = items[0].split(' / ').map(s => s.trim()).filter(Boolean);
    }
    if (items.length === 0) {
        return '<div class="text-muted" style="font-style:italic; font-size:12px; padding:4px 0;">Tidak ada daftar petugas tambahan</div>';
    }
    let html = '<div style="display:flex; flex-direction:column; gap:5px;">';
    items.forEach((name, idx) => {
        html += `
            <div style="display:flex; align-items:center; gap:8px; padding:5px 9px; background:rgba(241,245,249,0.7); border:1px solid #e2e8f0; border-radius:6px; font-size:12.5px;">
                <span style="display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; background:#e2e8f0; color:#475569; border-radius:50%; font-size:10.5px; font-weight:700; flex-shrink:0;">${idx + 1}</span>
                <span style="color:#1e293b; font-weight:500;">${escapeHtml(name)}</span>
            </div>
        `;
    });
    html += '</div>';
    return html;
}

function openPiketDetail(btn) {
    const hari = btn.getAttribute('data-hari') || '-';
    const isToday = btn.getAttribute('data-is-today') === '1';
    const waka = btn.getAttribute('data-waka') || '-';
    const wakaRole = btn.getAttribute('data-waka-role') || '-';
    const wakaHp = btn.getAttribute('data-waka-hp') || '';
    const koorPagi = btn.getAttribute('data-koor-pagi') || '-';
    const petugasPagi = btn.getAttribute('data-petugas-pagi') || '';
    const koorSiang = btn.getAttribute('data-koor-siang') || '-';
    const petugasSiang = btn.getAttribute('data-petugas-siang') || '';
    const keterangan = btn.getAttribute('data-keterangan') || '';

    document.getElementById('modalPiketTanggalText').textContent = hari;
    document.getElementById('modalPiketBadgeToday').style.display = isToday ? 'inline-block' : 'none';

    document.getElementById('modalPiketWakaNama').textContent = waka;
    document.getElementById('modalPiketWakaRole').textContent = wakaRole;

    const hpBox = document.getElementById('modalPiketWakaHpBox');
    if (wakaHp) {
        const cleanHp = wakaHp.replace(/[^0-9]/g, '');
        document.getElementById('modalPiketWakaHpLink').href = 'https://wa.me/' + cleanHp;
        document.getElementById('modalPiketWakaHpVal').textContent = wakaHp;
        hpBox.style.display = 'block';
    } else {
        hpBox.style.display = 'none';
    }

    document.getElementById('modalPiketKoorPagi').textContent = koorPagi;
    document.getElementById('modalPiketPetugasPagiList').innerHTML = renderTeacherList(petugasPagi);

    document.getElementById('modalPiketKoorSiang').textContent = koorSiang;
    document.getElementById('modalPiketPetugasSiangList').innerHTML = renderTeacherList(petugasSiang);

    const ketBox = document.getElementById('modalPiketKetBox');
    if (keterangan && keterangan.trim() !== '') {
        document.getElementById('modalPiketKetText').textContent = keterangan;
        ketBox.style.display = 'block';
    } else {
        ketBox.style.display = 'none';
    }

    const modal = document.getElementById('modalDetailPiket');
    modal.style.display = 'flex';
}

function closePiketDetail() {
    const modal = document.getElementById('modalDetailPiket');
    if (modal) modal.style.display = 'none';
}

// Close on backdrop click & ESC key
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalDetailPiket');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePiketDetail();
            }
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePiketDetail();
        }
    });
});
</script>
@endsection
