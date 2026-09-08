@extends('layouts.app')

@section('title', 'Detail Mapel — Jurnal Sekolah')
@section('page-title', 'Detail Mapel')

@push('styles')
<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.detail-page-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    animation: fadeSlideUp 0.4s ease both;
}
.detail-page-header .page-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0 0 3px; }
.detail-page-header .page-subtitle { font-size: 13px; color: var(--text-muted); margin: 0; }

.detail-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius-lg); box-shadow: var(--shadow-md);
    max-width: 640px; overflow: hidden;
    animation: fadeSlideUp 0.45s ease 0.1s both;
}

/* Hero Banner */
.mapel-hero {
    padding: 28px 24px;
    background: linear-gradient(135deg, var(--navy-primary) 0%, #1e3a8a 60%, #1d4ed8 100%);
    color: white;
    display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
}
.mapel-hero-icon {
    width: 60px; height: 60px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}
.mapel-hero-info h2 {
    font-size: 20px; font-weight: 800; margin: 0 0 6px; line-height: 1.25;
}
.mapel-hero-info .hero-badges { display: flex; gap: 8px; flex-wrap: wrap; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 999px;
    font-size: 12px; font-weight: 700;
    background: rgba(255,255,255,0.18);
    color: white;
    backdrop-filter: blur(4px);
}

.detail-body { padding: 24px; }

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
}
@media (max-width: 480px) {
    .info-grid { grid-template-columns: 1fr; }
}

.info-item {
    background: var(--bg-app);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 14px 16px;
}
.info-item.full-width { grid-column: 1 / -1; }
.info-item .info-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.info-item .info-value {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text-primary);
}
.info-item .info-value.monospace {
    font-family: 'Courier New', monospace;
    color: #2563eb;
    background: rgba(37,99,235,0.08);
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    display: inline-block;
}
[data-theme="dark"] .info-item .info-value.monospace {
    color: #60a5fa;
    background: rgba(37,99,235,0.15);
}

.tingkat-display {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
}
.tingkat-display.tx  { background: rgba(16,185,129,0.1); color: #059669; }
.tingkat-display.txi { background: rgba(245,158,11,0.1); color: #d97706; }
.tingkat-display.txii{ background: rgba(139,92,246,0.1); color: #7c3aed; }
.tingkat-display.tnil{ background: var(--badge-gray-bg); color: var(--badge-gray-text); }
[data-theme="dark"] .tingkat-display.tx  { background: rgba(16,185,129,0.18); color: #34d399; }
[data-theme="dark"] .tingkat-display.txi { background: rgba(245,158,11,0.18); color: #fbbf24; }
[data-theme="dark"] .tingkat-display.txii{ background: rgba(139,92,246,0.18); color: #a78bfa; }

.detail-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex; gap: 10px; flex-wrap: wrap;
    background: var(--bg-card-header, #fafbfc);
}
[data-theme="dark"] .detail-footer { background: rgba(255,255,255,0.02); }
</style>
@endpush

@section('content')

@php
    $t = $mapel->tingkat;
    $badgeClass = match($t) {
        'X'   => 'tx',
        'XI'  => 'txi',
        'XII' => 'txii',
        default => 'tnil'
    };
    $emoji = match($t) {
        'X' => '🟢', 'XI' => '🟡', 'XII' => '🟣',
        default => '⚪'
    };
@endphp

<div class="detail-page-header">
    <div>
        <h1 class="page-title">Detail Mata Pelajaran</h1>
        <p class="page-subtitle">Informasi lengkap master mata pelajaran kurikulum</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('mapel.edit', $mapel->id_mapel) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Mapel
        </a>
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<div class="detail-card">
    {{-- Hero --}}
    <div class="mapel-hero">
        <div class="mapel-hero-icon">📖</div>
        <div class="mapel-hero-info">
            <h2>{{ $mapel->nama_mapel }}</h2>
            <div class="hero-badges">
                @if($mapel->kode_mapel)
                <span class="hero-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                    </svg>
                    {{ $mapel->kode_mapel }}
                </span>
                @endif
                <span class="hero-badge">{{ $emoji }} Kelas {{ $t ?? '-' }}</span>
                <span class="hero-badge">ID: #{{ $mapel->id_mapel }}</span>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="detail-body">
        <div class="info-grid">
            <div class="info-item full-width">
                <div class="info-label">Nama Mata Pelajaran</div>
                <div class="info-value" style="font-size:16px;">{{ $mapel->nama_mapel }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Kode Mapel</div>
                <div class="info-value">
                    @if($mapel->kode_mapel)
                        <span class="monospace">{{ $mapel->kode_mapel }}</span>
                    @else
                        <span style="color:var(--text-muted);font-style:italic;font-weight:400;">Belum ada kode</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tingkat Kelas</div>
                <div class="info-value">
                    <span class="tingkat-display {{ $badgeClass }}">
                        {{ $emoji }} Kelas {{ $t ?? '-' }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Dibuat Pada</div>
                <div class="info-value" style="font-size:13px;font-weight:500;">
                    {{ $mapel->created_at ? $mapel->created_at->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Terakhir Diperbarui</div>
                <div class="info-value" style="font-size:13px;font-weight:500;">
                    {{ $mapel->updated_at ? $mapel->updated_at->format('d M Y, H:i') : '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="detail-footer">
        <a href="{{ route('mapel.edit', $mapel->id_mapel) }}" class="btn btn-primary btn-sm">
            Edit Data
        </a>
        <form action="{{ route('mapel.destroy', $mapel->id_mapel) }}" method="POST" style="display:inline;"
              onsubmit="return confirm('Hapus mata pelajaran ini?\nData akan dipindahkan ke trash.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
        </form>
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary btn-sm">← Kembali ke Daftar</a>
    </div>
</div>

@endsection
