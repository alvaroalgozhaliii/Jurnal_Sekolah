@extends('layouts.app')

@section('title', 'Mata Pelajaran — Jurnal Sekolah')
@section('page-title', 'Mata Pelajaran')

@push('styles')
<style>
/* ═══════════════════════════════════════
   MAPEL PAGE — Premium Redesign
═══════════════════════════════════════ */

/* Stat Cards */
.mapel-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

@media (max-width: 900px) {
    .mapel-stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 540px) {
    .mapel-stats { grid-template-columns: 1fr; }
}

.stat-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 18px 20px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    animation: fadeSlideUp 0.5s ease both;
}
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.10s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.20s; }

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    transition: height 0.3s ease;
}
.stat-card:hover::before { height: 4px; }

.stat-card.all::before   { background: linear-gradient(90deg, #2563eb, #1d4ed8); }
.stat-card.x::before     { background: linear-gradient(90deg, #10b981, #059669); }
.stat-card.xi::before    { background: linear-gradient(90deg, #f59e0b, #d97706); }
.stat-card.xii::before   { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

.stat-icon {
    width: 44px; height: 44px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-card.all  .stat-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
.stat-card.x    .stat-icon { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
.stat-card.xi   .stat-icon { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.stat-card.xii  .stat-icon { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }

[data-theme="dark"] .stat-card.all  .stat-icon { background: rgba(37,99,235,0.2); color: #60a5fa; }
[data-theme="dark"] .stat-card.x    .stat-icon { background: rgba(16,185,129,0.2); color: #34d399; }
[data-theme="dark"] .stat-card.xi   .stat-icon { background: rgba(245,158,11,0.2); color: #fbbf24; }
[data-theme="dark"] .stat-card.xii  .stat-icon { background: rgba(139,92,246,0.2); color: #a78bfa; }

.stat-info { flex: 1; min-width: 0; }
.stat-label { font-size: 11.5px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value { font-size: 28px; font-weight: 800; line-height: 1.2; margin-top: 2px; }
.stat-card.all  .stat-value { color: #1d4ed8; }
.stat-card.x    .stat-value { color: #059669; }
.stat-card.xi   .stat-value { color: #d97706; }
.stat-card.xii  .stat-value { color: #7c3aed; }

[data-theme="dark"] .stat-card.all  .stat-value { color: #60a5fa; }
[data-theme="dark"] .stat-card.x    .stat-value { color: #34d399; }
[data-theme="dark"] .stat-card.xi   .stat-value { color: #fbbf24; }
[data-theme="dark"] .stat-card.xii  .stat-value { color: #a78bfa; }

/* Toolbar */
.mapel-toolbar {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 14px 18px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-sm);
    animation: fadeSlideUp 0.5s ease 0.25s both;
}

.mapel-toolbar .search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.mapel-toolbar .search-wrap svg {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    width: 16px; height: 16px;
    color: var(--text-muted);
    pointer-events: none;
}
.mapel-toolbar .search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    background: var(--bg-app);
    color: var(--text-primary);
    transition: all 0.2s ease;
}
.mapel-toolbar .search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
    background: var(--bg-card);
}

.filter-pills {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.filter-pill {
    padding: 7px 14px;
    border-radius: 999px;
    border: 1.5px solid var(--border);
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    background: var(--bg-card);
    color: var(--text-secondary);
    white-space: nowrap;
}
.filter-pill:hover { border-color: #2563eb; color: #2563eb; background: rgba(37,99,235,0.06); }
.filter-pill.active-all    { background: #2563eb; border-color: #2563eb; color: #fff; }
.filter-pill.active-x      { background: #059669; border-color: #059669; color: #fff; }
.filter-pill.active-xi     { background: #d97706; border-color: #d97706; color: #fff; }
.filter-pill.active-xii    { background: #7c3aed; border-color: #7c3aed; color: #fff; }
.filter-pill.active-all:hover,
.filter-pill.active-x:hover,
.filter-pill.active-xi:hover,
.filter-pill.active-xii:hover { opacity: 0.9; }

/* Table */
.mapel-table-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: fadeSlideUp 0.5s ease 0.3s both;
}
.mapel-table-card .table-header-bar {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}
.mapel-table-card .table-title {
    font-weight: 700;
    font-size: 14px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}
.mapel-table-card .result-count {
    font-size: 12px;
    color: var(--text-muted);
    background: var(--bg-app);
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid var(--border);
}

.mapel-table {
    width: 100%;
    border-collapse: collapse;
}
.mapel-table thead tr {
    background: var(--bg-card-header, #fafbfc);
}
[data-theme="dark"] .mapel-table thead tr {
    background: rgba(255,255,255,0.03);
}
.mapel-table th {
    padding: 11px 16px;
    text-align: left;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.mapel-table td {
    padding: 13px 16px;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
    color: var(--text-primary);
    transition: background 0.15s ease;
}
.mapel-table tbody tr:last-child td { border-bottom: none; }
.mapel-table tbody tr {
    transition: background 0.15s ease;
    animation: rowFadeIn 0.35s ease both;
}
.mapel-table tbody tr:hover td { background: var(--bg-app); }

/* Row stagger animation */
@keyframes rowFadeIn {
    from { opacity: 0; transform: translateX(-6px); }
    to   { opacity: 1; transform: translateX(0); }
}
.mapel-table tbody tr:nth-child(1) { animation-delay: 0.05s; }
.mapel-table tbody tr:nth-child(2) { animation-delay: 0.08s; }
.mapel-table tbody tr:nth-child(3) { animation-delay: 0.11s; }
.mapel-table tbody tr:nth-child(4) { animation-delay: 0.14s; }
.mapel-table tbody tr:nth-child(5) { animation-delay: 0.17s; }
.mapel-table tbody tr:nth-child(n+6) { animation-delay: 0.20s; }

.badge-kode {
    display: inline-flex;
    align-items: center;
    background: var(--badge-info-bg);
    color: var(--badge-info-text);
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 12.5px;
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    letter-spacing: 0.3px;
}
[data-theme="dark"] .badge-kode {
    background: rgba(14,165,233,0.15);
    color: #38bdf8;
}

.badge-tingkat {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
}
.badge-tingkat.tx  { background: rgba(16,185,129,0.1); color: #059669; }
.badge-tingkat.txi { background: rgba(245,158,11,0.1); color: #d97706; }
.badge-tingkat.txii{ background: rgba(139,92,246,0.1); color: #7c3aed; }
.badge-tingkat.tnil{ background: var(--badge-gray-bg); color: var(--badge-gray-text); }

[data-theme="dark"] .badge-tingkat.tx  { background: rgba(16,185,129,0.18); color: #34d399; }
[data-theme="dark"] .badge-tingkat.txi { background: rgba(245,158,11,0.18); color: #fbbf24; }
[data-theme="dark"] .badge-tingkat.txii{ background: rgba(139,92,246,0.18); color: #a78bfa; }

.mapel-name { font-weight: 600; color: var(--text-primary); }
.mapel-name small { font-weight: 400; color: var(--text-muted); font-size: 11.5px; display: block; }

.action-btns { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }

/* Empty state */
.empty-mapel {
    padding: 60px 20px;
    text-align: center;
}
.empty-mapel-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    font-size: 32px;
}
[data-theme="dark"] .empty-mapel-icon {
    background: rgba(37,99,235,0.15);
}
.empty-mapel h4 { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
.empty-mapel p  { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

/* Global entry animations */
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Page header */
.page-header-mapel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    animation: fadeSlideUp 0.45s ease both;
}
.page-header-mapel .page-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 3px;
    line-height: 1.2;
}
.page-header-mapel .page-subtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}
.page-header-mapel .hdr-actions {
    display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
}

/* Responsive column hide */
@media (max-width: 640px) {
    .col-kode, .col-tingkat { display: none; }
    .mapel-table th, .mapel-table td { padding: 10px 12px; }
    .action-btns { flex-direction: column; }
    .mapel-toolbar { flex-direction: column; align-items: stretch; }
}
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header-mapel">
    <div>
        <h1 class="page-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;color:#2563eb;">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            </svg>
            Data Mata Pelajaran
        </h1>
        <p class="page-subtitle">Kelola master mata pelajaran kurikulum sekolah per tingkat kelas</p>
    </div>
    <div class="hdr-actions">
        <a href="{{ route('mapel.trash') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                <path d="M9 6V4h6v2"/>
            </svg>
            Trash
        </a>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Mapel
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success" style="
    background:linear-gradient(135deg,#d1fae5,#a7f3d0);
    border:1px solid #6ee7b7;
    color:#065f46;
    border-radius:var(--radius-md);
    padding:12px 16px;
    margin-bottom:16px;
    font-weight:600;
    font-size:13.5px;
    display:flex;align-items:center;gap:10px;
    animation:fadeSlideUp 0.4s ease both;">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="mapel-stats">
    <div class="stat-card all">
        <div class="stat-icon">📚</div>
        <div class="stat-info">
            <div class="stat-label">Total Mapel</div>
            <div class="stat-value">{{ $totalMapel }}</div>
        </div>
    </div>
    <div class="stat-card x">
        <div class="stat-icon">🟢</div>
        <div class="stat-info">
            <div class="stat-label">Kelas X</div>
            <div class="stat-value">{{ $groupByTingkat['X'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card xi">
        <div class="stat-icon">🟡</div>
        <div class="stat-info">
            <div class="stat-label">Kelas XI</div>
            <div class="stat-value">{{ $groupByTingkat['XI'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card xii">
        <div class="stat-icon">🟣</div>
        <div class="stat-info">
            <div class="stat-label">Kelas XII</div>
            <div class="stat-value">{{ $groupByTingkat['XII'] ?? 0 }}</div>
        </div>
    </div>
</div>

{{-- Toolbar: Search + Filter --}}
<form method="GET" action="{{ route('mapel.index') }}" id="filterForm">
<div class="mapel-toolbar">
    <div class="search-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" class="search-input"
            placeholder="Cari nama mapel, kode, atau tingkat..."
            value="{{ $search ?? '' }}"
            autocomplete="off">
    </div>

    <div class="filter-pills">
        <a href="{{ route('mapel.index', array_filter(['search' => $search])) }}"
           class="filter-pill {{ !$tingkat ? 'active-all' : '' }}">Semua</a>
        <a href="{{ route('mapel.index', array_filter(['search' => $search, 'tingkat' => 'X'])) }}"
           class="filter-pill {{ $tingkat === 'X' ? 'active-x' : '' }}">Kelas X</a>
        <a href="{{ route('mapel.index', array_filter(['search' => $search, 'tingkat' => 'XI'])) }}"
           class="filter-pill {{ $tingkat === 'XI' ? 'active-xi' : '' }}">Kelas XI</a>
        <a href="{{ route('mapel.index', array_filter(['search' => $search, 'tingkat' => 'XII'])) }}"
           class="filter-pill {{ $tingkat === 'XII' ? 'active-xii' : '' }}">Kelas XII</a>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    @if($search || $tingkat)
    <a href="{{ route('mapel.index') }}" class="btn btn-secondary btn-sm">Reset</a>
    @endif
</div>
</form>

{{-- Table Card --}}
<div class="mapel-table-card">
    <div class="table-header-bar">
        <div class="table-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#2563eb" stroke-width="2">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="2"/><path d="M9 12h6"/><path d="M9 16h6"/>
            </svg>
            Daftar Mata Pelajaran
        </div>
        <span class="result-count">
            {{ $mapel->count() }} data{{ ($search || $tingkat) ? ' ditemukan' : ' tersedia' }}
        </span>
    </div>

    @if($mapel->count() > 0)
    <div style="overflow-x:auto;">
        <table class="mapel-table">
            <thead>
                <tr>
                    <th style="width:48px;">No</th>
                    <th class="col-kode">Kode Mapel</th>
                    <th>Nama Mata Pelajaran</th>
                    <th class="col-tingkat" style="width:120px;">Tingkat</th>
                    <th style="width:170px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mapel as $item)
                @php
                    $t = $item->tingkat;
                    $badgeClass = match($t) {
                        'X'   => 'tx',
                        'XI'  => 'txi',
                        'XII' => 'txii',
                        default => 'tnil'
                    };
                    $tingkatIcon = match($t) {
                        'X'   => '🟢',
                        'XI'  => '🟡',
                        'XII' => '🟣',
                        default => '⚪'
                    };
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;font-size:12px;">
                        {{ $loop->iteration }}
                    </td>
                    <td class="col-kode">
                        <span class="badge-kode">{{ $item->kode_mapel ?? '—' }}</span>
                    </td>
                    <td>
                        <div class="mapel-name">
                            {{ $item->nama_mapel }}
                            <small>{{ $item->kode_mapel ?? 'Belum ada kode' }}</small>
                        </div>
                    </td>
                    <td class="col-tingkat">
                        <span class="badge-tingkat {{ $badgeClass }}">
                            {{ $tingkatIcon }} Kelas {{ $t ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns" style="justify-content:flex-end;">
                            <a href="{{ route('mapel.show', $item->id_mapel) }}"
                               class="btn btn-secondary btn-sm" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>Detail
                            </a>
                            <a href="{{ route('mapel.edit', $item->id_mapel) }}"
                               class="btn btn-primary btn-sm" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>Edit
                            </a>
                            <form action="{{ route('mapel.destroy', $item->id_mapel) }}" method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirmDelete(event, '{{ addslashes($item->nama_mapel) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-mapel">
        <div class="empty-mapel-icon">📖</div>
        <h4>Belum Ada Data Mata Pelajaran</h4>
        <p>
            @if($search || $tingkat)
                Tidak ada hasil untuk pencarian "<strong>{{ $search }}</strong>"
                @if($tingkat) dengan filter Kelas <strong>{{ $tingkat }}</strong>@endif.
            @else
                Mulai tambahkan mata pelajaran pertama untuk kurikulum sekolah.
            @endif
        </p>
        @if($search || $tingkat)
            <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Reset Pencarian</a>
        @else
            <a href="{{ route('mapel.create') }}" class="btn btn-primary">+ Tambah Mapel Pertama</a>
        @endif
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Live search with debounce
(function() {
    const input = document.querySelector('.search-input');
    if (!input) return;
    let debounceTimer;
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
})();

// Confirm delete with custom modal-like confirm
function confirmDelete(e, name) {
    return confirm('Hapus mata pelajaran "' + name + '"?\nData akan dipindahkan ke trash dan bisa di-restore.');
}

// Counter animation for stat values
(function() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const target = parseInt(el.textContent, 10);
        if (isNaN(target) || target === 0) return;
        let start = 0;
        const duration = 700;
        const step = timestamp => {
            if (!step.startTime) step.startTime = timestamp;
            const progress = Math.min((timestamp - step.startTime) / duration, 1);
            el.textContent = Math.round(progress * target);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });
})();
</script>
@endpush
