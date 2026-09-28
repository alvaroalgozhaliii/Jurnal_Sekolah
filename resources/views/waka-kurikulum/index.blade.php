@extends('layouts.app')

@section('title', 'Jadwal Piket & Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Piket & Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Piket &amp; Waka Bertugas</h1>
        <p class="page-subtitle">Manajemen penugasan Waka Piket dan Guru Piket — tampil per bulan</p>
    </div>
    <div class="page-actions" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('jadwal-piket.template-csv') }}" class="btn btn-secondary btn-sm" title="Download template format CSV">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Template CSV
        </a>
        <a href="{{ route('jadwal-piket.export-csv', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary btn-sm" title="Export jadwal bulan ini ke CSV">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
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
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau keterangan...">
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
                                <div class="text-muted" style="font-size:11.5px;">{{ $item->guruPiket->bidang_studi ?? 'Guru Piket' }}</div>
                            @else
                                <span class="text-muted"><em>Belum ditentukan</em></span>
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('jadwal-piket.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="{{ route('jadwal-piket.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penugasan tanggal ini?')">
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
                <div style="display:flex; gap:10px; justify-content:center; margin-top:12px;">
                    <a href="{{ route('jadwal-piket.create') }}" class="btn btn-primary btn-sm">+ Buat Jadwal Bulanan</a>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modalImportCsv').style.display='flex';">Upload CSV</button>
                </div>
            </div>
        @endif
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
                    Unggah file format <strong>.csv</strong> untuk menambahkan atau memperbarui jadwal piket harian. Kolom wajib: <code>tanggal</code>, <code>waka</code>. Kolom opsional: <code>guru_piket</code>, <code>keterangan</code>.
                </p>

                <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:20px; text-align:center; margin-bottom:16px;">
                    <svg class="svg-icon text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:36px;height:36px; margin-bottom:8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <div style="margin-bottom:8px;">
                        <input type="file" name="csv_file" id="csv_file_input" accept=".csv,text/csv" required style="font-size:13px;">
                    </div>
                    <span class="text-muted" style="font-size:11.5px;">Maksimal ukuran file: 10 MB</span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:#eff6ff; border-radius:6px;">
                    <span style="font-size:12px; color:#1e40af;">Belum punya formatnya?</span>
                    <a href="{{ route('jadwal-piket.template-csv') }}" class="btn btn-secondary btn-sm" style="font-size:11px; padding:4px 10px;">
                        Download Template CSV
                    </a>
                </div>
            </div>
            <div class="card-footer" style="display:flex; justify-content:flex-end; gap:10px; padding:12px 20px; background:#f8fafc;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalImportCsv').style.display='none';">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight:600;">Unggah &amp; Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
