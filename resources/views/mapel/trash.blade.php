@extends('layouts.app')

@section('title', 'Trash Mapel — Jurnal Sekolah')
@section('page-title', 'Trash Mapel')

@push('styles')
<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes rowFadeIn {
    from { opacity: 0; transform: translateX(-6px); }
    to   { opacity: 1; transform: translateX(0); }
}

.trash-page-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    animation: fadeSlideUp 0.4s ease both;
}
.trash-page-header .page-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0 0 3px; }
.trash-page-header .page-subtitle { font-size: 13px; color: var(--text-muted); margin: 0; }

/* Warning banner */
.trash-warning {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 18px;
    background: rgba(239,68,68,0.06);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    font-size: 13px;
    color: var(--text-secondary);
    animation: fadeSlideUp 0.4s ease 0.05s both;
}
.trash-warning svg { flex-shrink: 0; margin-top: 1px; }
.trash-warning strong { color: #dc2626; }

.trash-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: fadeSlideUp 0.45s ease 0.1s both;
}
.trash-card-header {
    padding: 14px 20px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
    background: rgba(239,68,68,0.03);
}
.trash-card-title {
    font-weight: 700; font-size: 14px; color: var(--text-primary);
    display: flex; align-items: center; gap: 8px;
}
.trash-count-badge {
    font-size: 12px; color: #dc2626; background: rgba(239,68,68,0.1);
    padding: 3px 10px; border-radius: 999px; border: 1px solid rgba(239,68,68,0.2);
}

.trash-table { width: 100%; border-collapse: collapse; }
.trash-table thead tr { background: rgba(239,68,68,0.03); }
.trash-table th {
    padding: 11px 16px; text-align: left;
    font-size: 11.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.5px; color: var(--text-secondary);
    border-bottom: 1px solid var(--border);
}
.trash-table td {
    padding: 13px 16px; border-bottom: 1px solid var(--border);
    font-size: 13px; color: var(--text-primary);
}
.trash-table tbody tr:last-child td { border-bottom: none; }
.trash-table tbody tr { transition: background 0.15s ease; animation: rowFadeIn 0.35s ease both; }
.trash-table tbody tr:hover td { background: rgba(239,68,68,0.03); }
.trash-table tbody tr:nth-child(1) { animation-delay: 0.05s; }
.trash-table tbody tr:nth-child(2) { animation-delay: 0.08s; }
.trash-table tbody tr:nth-child(n+3) { animation-delay: 0.12s; }

.badge-kode-trash {
    display: inline-flex; align-items: center;
    background: rgba(239,68,68,0.08); color: #dc2626;
    font-family: 'Courier New', monospace; font-weight: 700; font-size: 12.5px;
    padding: 4px 10px; border-radius: var(--radius-sm); letter-spacing: 0.3px;
}
[data-theme="dark"] .badge-kode-trash { background: rgba(239,68,68,0.15); color: #f87171; }

.mapel-name-trash { font-weight: 600; color: var(--text-secondary); text-decoration: line-through; opacity: 0.75; }

.action-btns { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }

.empty-trash {
    padding: 60px 20px; text-align: center;
}
.empty-trash-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 32px;
}
[data-theme="dark"] .empty-trash-icon { background: rgba(16,185,129,0.15); }
.empty-trash h4 { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
.empty-trash p  { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

@media (max-width: 560px) {
    .col-kode-trash { display: none; }
    .action-btns { flex-direction: column; }
}
</style>
@endpush

@section('content')

<div class="trash-page-header">
    <div>
        <h1 class="page-title">
            🗑️ Trash — Mata Pelajaran
        </h1>
        <p class="page-subtitle">Data mapel yang dihapus sementara. Bisa di-restore atau dihapus permanen.</p>
    </div>
    <a href="{{ route('mapel.index') }}" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>

@if($mapel->count() > 0)
<div class="trash-warning">
    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
        stroke="#dc2626" stroke-width="2">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/>
        <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <div>
        Terdapat <strong>{{ $mapel->count() }} mapel</strong> di trash.
        Data masih bisa di-restore. <strong>Hapus Permanen tidak bisa dibatalkan!</strong>
    </div>
</div>
@endif

<div class="trash-card">
    <div class="trash-card-header">
        <div class="trash-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#dc2626" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6"/><path d="M14 11v6"/>
                <path d="M9 6V4h6v2"/>
            </svg>
            Data Terhapus (Soft Delete)
        </div>
        @if($mapel->count() > 0)
        <span class="trash-count-badge">{{ $mapel->count() }} item di trash</span>
        @endif
    </div>

    @if($mapel->count() > 0)
    <div style="overflow-x:auto;">
        <table class="trash-table">
            <thead>
                <tr>
                    <th style="width:48px;">No</th>
                    <th class="col-kode-trash">Kode Mapel</th>
                    <th>Nama Mata Pelajaran</th>
                    <th style="width:100px;">Tingkat</th>
                    <th style="width:220px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mapel as $item)
                @php
                    $t = $item->tingkat;
                    $emoji = match($t) { 'X' => '🟢', 'XI' => '🟡', 'XII' => '🟣', default => '⚪' };
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;font-size:12px;">{{ $loop->iteration }}</td>
                    <td class="col-kode-trash">
                        <span class="badge-kode-trash">{{ $item->kode_mapel ?? '—' }}</span>
                    </td>
                    <td>
                        <div class="mapel-name-trash">{{ $item->nama_mapel }}</div>
                        @if($item->deleted_at)
                        <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">
                            Dihapus: {{ $item->deleted_at->diffForHumans() }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:12px;color:var(--text-muted);">{{ $emoji }} Kelas {{ $t ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="action-btns" style="justify-content:flex-end;">
                            <form action="{{ route('mapel.restore', $item->id_mapel) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                        <path d="M3 3v5h5"/>
                                    </svg>
                                    Restore
                                </button>
                            </form>
                            <form action="{{ route('mapel.forceDelete', $item->id_mapel) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('HAPUS PERMANEN?\n\'{{ addslashes($item->nama_mapel) }}\'\nTindakan ini tidak bisa dibatalkan!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                    Hapus Permanen
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
    <div class="empty-trash">
        <div class="empty-trash-icon">✅</div>
        <h4>Trash Kosong</h4>
        <p>Tidak ada data mata pelajaran yang dihapus.</p>
        <a href="{{ route('mapel.index') }}" class="btn btn-primary">← Kembali ke Daftar Mapel</a>
    </div>
    @endif
</div>

@endsection
