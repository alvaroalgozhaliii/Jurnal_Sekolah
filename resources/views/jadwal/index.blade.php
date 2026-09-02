@extends('layouts.app')

@section('title', 'Jadwal Pelajaran — Jurnal Sekolah')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Pelajaran KBM</h1>
        <p class="page-subtitle">Kelola Master Jadwal Pelajaran Terstruktur Berdasarkan Kolom / Folder Kelas</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jadwal.create') }}" class="btn btn-primary">+ Tambah Jadwal Baru</a>
        <a href="{{ route('jadwal.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- CSV Import Card --}}
<div class="card mb-16" style="background:#f8fafc; border:1px dashed #cbd5e1;">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong style="color:var(--text-navy, #1e293b); font-size:14px;">&#x1F4E5; Import Data Jadwal via CSV:</strong>
                <a href="{{ route('jadwal.import-template') }}" class="btn btn-secondary btn-sm" style="font-size:12px; padding:4px 10px;">
                    &#x2B07; Download Template CSV
                </a>
            </div>
            <form action="{{ route('jadwal.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required style="font-size:12px;">
                <button type="submit" class="btn btn-primary btn-sm">&#x1F4E4; Upload &amp; Import</button>
            </form>
        </div>
    </div>
</div>

{{-- Bar Pencarian & Filter Tingkat --}}
<div class="card mb-20">
    <div class="card-body" style="padding:16px;">
        <div class="d-flex justify-between align-center flex-wrap gap-12">
            {{-- Form Search --}}
            <form method="GET" action="{{ route('jadwal.index') }}" class="d-flex gap-8" style="flex:1; max-width:480px;">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                @if($tingkatFilter)
                    <input type="hidden" name="tingkat" value="{{ $tingkatFilter }}">
                @endif
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama kelas, jurusan, wali kelas, mapel..." class="form-control">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                @if($search)
                    <a href="{{ route('jadwal.index', ['view' => $viewMode, 'tingkat' => $tingkatFilter]) }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>

            {{-- Filter Tabs & Mode Tampilan --}}
            <div class="d-flex gap-8 align-center flex-wrap">
                {{-- Filter Tingkat --}}
                <div class="d-flex gap-4">
                    <a href="{{ route('jadwal.index', ['view' => $viewMode, 'search' => $search]) }}" class="btn btn-sm {{ !$tingkatFilter ? 'btn-navy' : 'btn-secondary' }}">Semua</a>
                    <a href="{{ route('jadwal.index', ['tingkat' => 'X', 'view' => $viewMode, 'search' => $search]) }}" class="btn btn-sm {{ $tingkatFilter === 'X' ? 'btn-navy' : 'btn-secondary' }}">Kelas X</a>
                    <a href="{{ route('jadwal.index', ['tingkat' => 'XI', 'view' => $viewMode, 'search' => $search]) }}" class="btn btn-sm {{ $tingkatFilter === 'XI' ? 'btn-navy' : 'btn-secondary' }}">Kelas XI</a>
                    <a href="{{ route('jadwal.index', ['tingkat' => 'XII', 'view' => $viewMode, 'search' => $search]) }}" class="btn btn-sm {{ $tingkatFilter === 'XII' ? 'btn-navy' : 'btn-secondary' }}">Kelas XII</a>
                </div>

                {{-- Mode Switcher: Folder Cards vs Tabel --}}
                <div class="d-flex gap-4" style="border-left:1px solid #cbd5e1; padding-left:8px;">
                    <a href="{{ route('jadwal.index', ['view' => 'folder', 'tingkat' => $tingkatFilter, 'search' => $search]) }}" class="btn btn-sm {{ $viewMode === 'folder' ? 'btn-primary' : 'btn-secondary' }}" title="Tampilan Folder Kelas">
                        📂 Folder Kelas
                    </a>
                    <a href="{{ route('jadwal.index', ['view' => 'table', 'tingkat' => $tingkatFilter, 'search' => $search]) }}" class="btn btn-sm {{ $viewMode === 'table' ? 'btn-primary' : 'btn-secondary' }}" title="Tampilan Tabel Semua">
                        📋 Tabel Ringkas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($search)
<p class="text-muted mb-16">Menampilkan hasil pencarian untuk: <strong>{{ $search }}</strong></p>
@endif

{{-- ========================================================================= --}}
{{-- 1. MODE TAMPILAN: FOLDER / KOLOM KELAS (DEFAULT) --}}
{{-- ========================================================================= --}}
@if($viewMode === 'folder')
    @if($kelasList->count() > 0)
    <div class="grid-3 mb-24">
        @foreach($kelasList as $k)
        @php
            $groupedJadwal = $k->jadwal->groupBy('hari');
            $seninCount = $groupedJadwal->has('Senin') ? $groupedJadwal['Senin']->count() : 0;
            $selasaCount = $groupedJadwal->has('Selasa') ? $groupedJadwal['Selasa']->count() : 0;
            $rabuCount = $groupedJadwal->has('Rabu') ? $groupedJadwal['Rabu']->count() : 0;
            $kamisCount = $groupedJadwal->has('Kamis') ? $groupedJadwal['Kamis']->count() : 0;
            $jumatCount = $groupedJadwal->has('Jumat') ? $groupedJadwal['Jumat']->count() : 0;
        @endphp
        <div class="card card-folder mb-24" style="border-top: 4px solid var(--navy-primary); display:flex; flex-direction:column; justify-content:space-between;">
            {{-- Header Folder --}}
            <div class="card-header d-flex justify-between align-center" style="padding:14px 16px;">
                <div class="d-flex align-center gap-8">
                    <span style="font-size:24px;">📁</span>
                    <div>
                        <h3 class="card-title" style="font-size:16px; margin:0;">{{ $k->nama_kelas }}</h3>
                        <span style="font-size:12px;" class="text-muted">{{ $k->jurusan->nama_jurusan ?? 'Tanpa Jurusan' }}</span>
                    </div>
                </div>
                <div>
                    <span class="badge badge-navy">Tingkat {{ $k->tingkat }}</span>
                </div>
            </div>

            {{-- Body Card --}}
            <div class="card-body" style="padding:16px;">
                {{-- Info Wali Kelas & Siswa --}}
                <div style="font-size:13px; margin-bottom:12px;">
                    <div class="d-flex justify-between py-4" style="border-bottom:1px dashed var(--border);">
                        <span class="text-muted">👨‍🏫 Wali Kelas:</span>
                        <strong class="text-navy">{{ $k->wali_kelas ?: '-' }}</strong>
                    </div>
                    <div class="d-flex justify-between py-4" style="border-bottom:1px dashed var(--border);">
                        <span class="text-muted">👥 Jumlah Siswa:</span>
                        <strong>{{ $k->siswa_count }} Siswa</strong>
                    </div>
                    <div class="d-flex justify-between py-4">
                        <span class="text-muted">📅 Total Jadwal:</span>
                        <strong style="color:{{ $k->jadwal_count > 0 ? '#16a34a' : '#dc2626' }}; font-size:14px;">
                            {{ $k->jadwal_count }} Sesi Pelajaran
                        </strong>
                    </div>
                </div>

                {{-- Badges Per Hari --}}
                <div class="distribusi-box">
                    <div style="font-size:11px; font-weight:700; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;" class="text-muted">Distribusi Hari:</div>
                    <div class="d-flex gap-4 flex-wrap" style="font-size:11px;">
                        <span class="badge {{ $seninCount > 0 ? 'badge-info' : 'badge-gray' }}" title="Senin">Sen: {{ $seninCount }}</span>
                        <span class="badge {{ $selasaCount > 0 ? 'badge-info' : 'badge-gray' }}" title="Selasa">Sel: {{ $selasaCount }}</span>
                        <span class="badge {{ $rabuCount > 0 ? 'badge-info' : 'badge-gray' }}" title="Rabu">Rab: {{ $rabuCount }}</span>
                        <span class="badge {{ $kamisCount > 0 ? 'badge-info' : 'badge-gray' }}" title="Kamis">Kam: {{ $kamisCount }}</span>
                        <span class="badge {{ $jumatCount > 0 ? 'badge-info' : 'badge-gray' }}" title="Jumat">Jum: {{ $jumatCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="card-footer d-flex gap-8" style="padding:12px 16px;">
                <a href="{{ route('kelas.show', $k->id_kelas) }}" class="btn btn-primary btn-sm" style="flex:1; text-align:center;">
                    📂 Kelola Jadwal Kelas
                </a>
                <a href="{{ route('jadwal.create', ['id_kelas' => $k->id_kelas]) }}" class="btn btn-secondary btn-sm" title="Tambah Jadwal untuk kelas ini">
                    ➕ Tambah
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-text">
                    @if($search || $tingkatFilter)
                        Tidak ada folder kelas yang sesuai dengan kriteria pencarian/filter.
                    @else
                        Belum ada data kelas untuk jadwal pelajaran.
                    @endif
                </div>
                <a href="{{ route('kelas.create') }}" class="btn btn-primary mt-16">+ Tambah Kelas Baru</a>
            </div>
        </div>
    </div>
    @endif

{{-- ========================================================================= --}}
{{-- 2. MODE TAMPILAN: TABEL PER HARI --}}
{{-- ========================================================================= --}}
@else
    @php
    $urutan = ['Senin','Selasa','Rabu','Kamis','Jumat'];
    $jadwalGrouped = $jadwal->groupBy('hari');
    @endphp

    @if($jadwal->count() > 0)
        @foreach($urutan as $hari)
            @if(isset($jadwalGrouped[$hari]))
            @php $jadwalHari = $jadwalGrouped[$hari]->sortBy('jam_ke'); @endphp
            <div class="card mb-16">
                <div class="card-header" style="background:#1e3a8a;">
                    <h3 class="card-title" style="color:#ffffff; margin:0;">
                        📅 {{ strtoupper($hari) }}
                    </h3>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrapper" style="border:none; border-radius:0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:80px;">Jam Ke</th>
                                    <th style="width:150px;">Jam Pembelajaran</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Guru Pengajar</th>
                                    <th style="width:80px;">Ruang</th>
                                    <th style="width:80px;">Status</th>
                                    <th class="action-col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalHari as $item)
                                <tr>
                                    <td class="fw-bold text-center">Jam {{ $item->jam_ke }}</td>
                                    <td class="fw-bold" style="color:#1e3a8a;">
                                        {{ \App\Services\KbmService::getLabelWaktu($item->hari, $item->jam_ke, $item->kelas->tingkat ?? null) ?: ($item->waktu_mulai . ' - ' . $item->waktu_selesai) }}
                                    </td>
                                    <td class="fw-bold text-navy">{{ $item->mapel }}</td>
                                    <td>
                                        <a href="{{ route('kelas.show', $item->id_kelas) }}" class="badge badge-navy" title="Lihat detail kelas">
                                            {{ $item->kelas->nama_kelas ?? '-' }}
                                        </a>
                                    </td>
                                    <td>{{ $item->guru->nama ?? '-' }}</td>
                                    <td>{{ $item->ruang ?? '-' }}</td>
                                    <td>
                                        @if($item->aktif)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="action-col">
                                        <a href="{{ route('jadwal.edit', $item->id_jadwal) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @else
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-text">
                    Tidak ada jadwal yang sesuai pencarian.
                </div>
            </div>
        </div>
    </div>
    @endif
@endif
@endsection