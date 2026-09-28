@extends('layouts.app')

@section('title', 'Pengaturan Jam Sekolah — Jurnal Sekolah')
@section('page-title', 'Pengaturan Jam Sekolah')

@section('content')
<style>
/* ==========================================================================
   PENGATURAN JAM SEKOLAH — MODERN & INTUITIVE DESIGN
   ========================================================================== */
.jam-wrapper {
    max-width: 1140px;
    margin: 0 auto;
}

/* Hero Header */
.jam-hero-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
    color: #ffffff;
    border-radius: var(--radius-lg, 14px);
    padding: 24px 28px;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(37,99,235,0.25);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.jam-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(8px);
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: #e0e7ff;
    margin-bottom: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.jam-hero-title {
    margin: 0 0 6px;
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
}

.jam-hero-desc {
    margin: 0;
    font-size: 13.5px;
    color: #cbd5e1;
    max-width: 620px;
    line-height: 1.5;
}

/* Metric Pill Cards Row */
.jam-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.jam-metric-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-left: 4px solid var(--border);
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                border-color 0.25s ease;
}

.jam-metric-card:hover {
    transform: translateY(-4px) scale(1.008);
    box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.12), 0 6px 12px -4px rgba(15, 23, 42, 0.08);
    border-top-color: rgba(59, 130, 246, 0.4);
    border-right-color: rgba(59, 130, 246, 0.4);
    border-bottom-color: rgba(59, 130, 246, 0.4);
}

.jam-metric-card:hover .jam-metric-icon svg {
    transform: scale(1.15) rotate(4deg);
}

.jam-metric-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.jam-metric-icon.blue { background: rgba(37,99,235,0.12); color: #2563eb; }
.jam-metric-icon.emerald { background: rgba(16,185,129,0.12); color: #10b981; }
.jam-metric-icon.purple { background: rgba(139,92,246,0.12); color: #8b5cf6; }
.jam-metric-icon.amber { background: rgba(245,158,11,0.12); color: #f59e0b; }

[data-theme="dark"] .jam-metric-icon.blue { background: rgba(37,99,235,0.25); color: #60a5fa; }
[data-theme="dark"] .jam-metric-icon.emerald { background: rgba(16,185,129,0.25); color: #34d399; }
[data-theme="dark"] .jam-metric-icon.purple { background: rgba(139,92,246,0.25); color: #a78bfa; }
[data-theme="dark"] .jam-metric-icon.amber { background: rgba(245,158,11,0.25); color: #fbbf24; }

.jam-metric-icon svg {
    width: 22px;
    height: 22px;
}

.jam-metric-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 2px;
}

.jam-metric-value {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-primary);
    font-family: monospace;
    line-height: 1.2;
}

.jam-metric-sub {
    font-size: 11.5px;
    color: var(--text-secondary);
    margin-top: 2px;
}

/* Settings Form Grid */
.jam-settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.jam-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 22px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
}

.jam-box-head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}

.jam-box-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
}

.jam-box-badge {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 999px;
    background: var(--badge-navy-bg);
    color: var(--navy-primary);
}

/* Friday Special Card Callout */
.friday-notice-callout {
    background: rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.25);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.friday-notice-callout svg {
    color: #8b5cf6;
    flex-shrink: 0;
    margin-top: 2px;
}

.friday-notice-title {
    font-size: 13px;
    font-weight: 700;
    color: #6d28d9;
    margin-bottom: 2px;
}

[data-theme="dark"] .friday-notice-title {
    color: #c4b5fd;
}

.friday-notice-desc {
    font-size: 12.5px;
    color: var(--text-secondary);
    line-height: 1.45;
    margin: 0;
}

/* Tabs for Slot Editor */
.jam-tabs-container {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.jam-tabs-header {
    background: var(--bg-card-header);
    border-bottom: 1px solid var(--border);
    padding: 14px 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.jam-tab-pills {
    display: flex;
    gap: 6px;
    overflow-x: auto;
}

.jam-tab-pill {
    background: transparent;
    border: 1px solid transparent;
    border-bottom: none;
    padding: 10px 18px;
    border-radius: 8px 8px 0 0;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    position: relative;
    top: 1px;
}

.jam-tab-pill:hover {
    color: var(--navy-primary);
    background: rgba(37,99,235,0.06);
}

.jam-tab-pill.active {
    background: var(--bg-card);
    color: #2563eb;
    border-color: var(--border);
    border-top: 2.5px solid #2563eb;
}

[data-theme="dark"] .jam-tab-pill.active {
    color: #60a5fa;
    border-top-color: #60a5fa;
    background: var(--bg-card);
}

.jam-tab-body {
    padding: 20px;
}

.jam-tab-pane {
    display: none;
    animation: fadeIn 0.2s ease-in-out;
}

.jam-tab-pane.active {
    display: block;
}

/* Slot Table */
.slot-tbl-wrap {
    overflow-x: auto;
    border: 1px solid var(--border);
    border-radius: 8px;
}

.slot-tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.slot-tbl th {
    background: var(--bg-card-header);
    padding: 12px 16px;
    font-weight: 700;
    color: var(--text-primary);
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.slot-tbl td {
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

.slot-tbl tr:last-child td {
    border-bottom: none;
}

.slot-tbl tr.row-break td {
    background: rgba(245, 158, 11, 0.08);
    color: #b45309;
    font-weight: 600;
}

[data-theme="dark"] .slot-tbl tr.row-break td {
    background: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
}

.slot-tbl tr.row-highlight-x td {
    background: rgba(139, 92, 246, 0.08);
}

[data-theme="dark"] .slot-tbl tr.row-highlight-x td {
    background: rgba(139, 92, 246, 0.18);
}

.badge-jp {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
    background: var(--badge-navy-bg);
/* Kepulangan Kondisional & Acara Khusus Styles */
.jam-kondisional-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.jam-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}

.alert-acara-mendadak {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(249, 115, 22, 0.12) 100%);
    border: 1.5px solid #f87171;
    border-left: 5px solid #ef4444;
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 24px;
    color: var(--text-primary);
    animation: fadeIn 0.3s ease;
}

[data-theme="dark"] .alert-acara-mendadak {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(249, 115, 22, 0.2) 100%);
    border-color: #f87171;
}

@keyframes pulseGlow {
    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
    70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.pulse-red-dot {
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    animation: pulseGlow 1.5s infinite;
}
</style>

<div class="jam-wrapper">

{{-- Banner Info Keterhubungan Antar Role --}}
<div class="jam-card-hero">
    <div>
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 700; margin-bottom: 4px;">
            INTEGRASI SISTEM REAL-TIME
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <form action="{{ route('admin.jam-sekolah.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh konfigurasi jam sekolah ke jadwal standar resmi SMKN 1 Boyolangu?');">
                @csrf
                <button type="submit" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.2"/></svg>
                    Reset ke Standar KBM
                </button>
            </form>
        </div>
    </div>

    {{-- Hero Banner Real-Time Sync --}}
    <div class="jam-hero-banner">
        <div>
            <div class="jam-hero-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                Sinkronisasi Otomatis Seluruh Role
            </div>
            <h2 class="jam-hero-title">Pusat Kendali Jadwal & Waktu KBM</h2>
            <p class="jam-hero-desc">
                Pengaturan ini langsung tersinkron ke <strong>Live Clock Banner</strong> di dashboard semua role, validasi kehadiran guru, pencatatan siswa terlambat di piket, serta batas waktu pengisian jurnal mengajar.
            </p>
        </div>
        <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; padding: 14px 20px; text-align: center; min-width: 210px;">
            <div style="font-size: 11px; text-transform: uppercase; color: #bfdbfe; font-weight: 700; letter-spacing: 0.5px;">Jam Masuk Sekolah</div>
            <div style="font-size: 26px; font-weight: 800; font-family: monospace; margin: 3px 0; color: #ffffff;">{{ $jamMasuk }} WIB</div>
            <div style="font-size: 12px; color: #86efac; font-weight: 600;">Toleransi: +{{ $toleransiTerlambat }} Menit</div>
        </div>
    </div>

    {{-- ALERT BANNER JIKA ACARA MENDADAK SEDANG AKTIF --}}
    @if($acaraMendadakConfig['aktif'])
        <div class="alert-acara-mendadak">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:14px;">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div style="width:42px; height:42px; border-radius:10px; background:rgba(239,68,68,0.2); color:#ef4444; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <div class="pulse-red-dot"></div>
                    </div>
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px; flex-wrap:wrap;">
                            <span class="badge" style="background:#ef4444; color:#fff; font-weight:800; font-size:11.5px; padding:3px 10px; letter-spacing:0.5px;">⚡ ACARA MENDADAK AKTIF</span>
                            <span style="font-size:12.5px; color:var(--text-secondary); font-weight:600;">Tanggal: {{ \Carbon\Carbon::parse($acaraMendadakConfig['tanggal'])->translatedFormat('l, d F Y') }}</span>
                            <span class="badge badge-info" style="font-size:11px;">Sasaran: {{ strtoupper($acaraMendadakConfig['target']) }}</span>
                        </div>
                        <div style="font-size:17px; font-weight:800; color:var(--text-primary); margin-bottom:4px;">
                            Siswa Dipulangkan Lebih Cepat Pukul <span style="font-family:monospace; background:rgba(239,68,68,0.15); padding:2px 8px; border-radius:6px; color:#ef4444;">{{ $acaraMendadakConfig['jam_pulang'] }} WIB</span>
                        </div>
                        <div style="font-size:13.5px; color:var(--text-secondary); line-height:1.4;">
                            <strong>Keterangan / Alasan:</strong> {{ $acaraMendadakConfig['alasan'] }}
                        </div>
                    </div>
                </div>
                <div>
                    <form action="{{ route('admin.jam-sekolah.acara-mendadak') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan mode acara mendadak dan mengembalikan jadwal kepulangan ke waktu reguler?');">
                        @csrf
                        <input type="hidden" name="aktif" value="0">
                        <button type="submit" class="btn btn-secondary btn-sm" style="background:#fff; border-color:#fca5a5; color:#b91c1c; font-weight:700; display:inline-flex; align-items:center; gap:6px; box-shadow:var(--shadow-sm);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Nonaktifkan Pulang Cepat
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Metric Highlights --}}
    <div class="jam-metrics-grid">
        {{-- Metric 1: Jam Masuk --}}
        <div class="jam-metric-card" style="border-left: 4px solid #0284c7;">
            <div class="jam-metric-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <div class="jam-metric-label">Jam Masuk Pagi</div>
                <div class="jam-metric-value">{{ $jamMasuk }} WIB</div>
                <div class="jam-metric-sub">Semua Tingkat (X, XI, XII)</div>
            </div>
        </div>

        {{-- Metric 2: Pulang Senin-Kamis --}}
        <div class="jam-metric-card" style="border-left: 4px solid {{ !$seninAdaUpacara ? '#ea580c' : '#16a34a' }};">
            <div class="jam-metric-icon {{ !$seninAdaUpacara ? 'amber' : 'emerald' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div>
                <div class="jam-metric-label">Pulang Senin–Kamis</div>
                @if(!$seninAdaUpacara)
                    <div class="jam-metric-value" style="color: #ea580c;">{{ $jamPulangSeninEfektif }} WIB</div>
                    <div class="jam-metric-sub">
                        <span class="badge" style="background:#ffedd5; color:#c2410c; font-size:10.5px; font-weight:700;">Senin: Tanpa Upacara (-40m)</span>
                    </div>
                @else
                    <div class="jam-metric-value">{{ $jamPulang }} WIB</div>
                    <div class="jam-metric-sub">10 JP (40 Menit/JP • Upacara Normal)</div>
                @endif
            </div>
        </div>

        {{-- Metric 3: Pulang Jumat Kelas 10 --}}
        <div class="jam-metric-card" style="border-left: 4px solid {{ !$jumatAdaPembiasaan ? '#ea580c' : '#9333ea' }};">
            <div class="jam-metric-icon {{ !$jumatAdaPembiasaan ? 'amber' : 'purple' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <div class="jam-metric-label">Pulang Jumat (Kelas 10)</div>
                @if(!$jumatAdaPembiasaan)
                    <div class="jam-metric-value" style="color: #ea580c;">{{ $jamPulangJumatXEfektif }} WIB</div>
                    <div class="jam-metric-sub">
                        <span class="badge" style="background:#ffedd5; color:#c2410c; font-size:10.5px; font-weight:700;">Tanpa Pembiasaan (-30m)</span>
                    </div>
                @else
                    <div class="jam-metric-value">{{ $jamPulangJumatX }} WIB</div>
                    <div class="jam-metric-sub">13 JP (Sampai Jam ke-13)</div>
                @endif
            </div>
        </div>

        {{-- Metric 4: Pulang Jumat Kelas 11 & 12 --}}
        <div class="jam-metric-card" style="border-left: 4px solid {{ !$jumatAdaPembiasaan ? '#ea580c' : '#d97706' }};">
            <div class="jam-metric-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
            <div>
                <div class="jam-metric-label">Pulang Jumat (Kelas 11 & 12)</div>
                @if(!$jumatAdaPembiasaan)
                    <div class="jam-metric-value" style="color: #ea580c;">{{ $jamPulangJumatXiEfektif }} WIB</div>
                    <div class="jam-metric-sub">
                        <span class="badge" style="background:#ffedd5; color:#c2410c; font-size:10.5px; font-weight:700;">Tanpa Pembiasaan (-30m)</span>
                    </div>
                @else
                    <div class="jam-metric-value">{{ $jamPulangJumatXi }} WIB</div>
                    <div class="jam-metric-sub">12 JP (Pulang Lebih Awal)</div>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================================
         SECTION KHUSUS: PENGATURAN KEPULANGAN KONDISIONAL & ACARA MENDADAK
         ========================================================================= --}}
    <div class="jam-kondisional-grid">

        {{-- BOX 1: PENYESUAIAN RUTIN (UPACARA & PEMBIASAAN) --}}
        <div class="jam-box" style="margin-bottom:0;">
            <div class="jam-box-head">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <h3 class="jam-box-title">Kepulangan Kondisional</h3>
                <span class="jam-box-badge">Hari Ini</span>
            </div>

            {{-- Row: Upacara Senin --}}
            <div class="jam-item-row" style="padding-bottom:14px;">
                <div style="flex:1; min-width:0;">
                    <div class="d-flex align-center gap-8 mb-4">
                        <strong style="font-size:13px; color:var(--text-primary);">Upacara Bendera (Senin)</strong>
                        <span class="badge {{ $seninAdaUpacara ? 'badge-success' : 'badge-amber' }}">
                            {{ $seninAdaUpacara ? 'Ada Upacara' : 'Maju 1 JP' }}
                        </span>
                    </div>
                    <div style="font-size:12px; color:var(--text-secondary); line-height:1.4;">
                        @if($seninAdaUpacara)
                            KBM normal, pulang pukul <strong style="color:var(--text-primary);">{{ $jamPulangSeninNormal }} WIB</strong>
                        @else
                            Tanpa upacara, pulang maju ke <strong style="color:var(--gold-accent, #f59e0b);">{{ $jamPulangSeninTanpaUpacara }} WIB</strong> (-40m)
                        @endif
                    </div>
                </div>
                <form action="{{ route('admin.jam-sekolah.toggle-upacara') }}" method="POST" style="margin:0; flex-shrink:0;">
                    @csrf
                    @if($seninAdaUpacara)
                        <input type="hidden" name="status" value="0">
                        <button type="submit" class="btn btn-secondary btn-sm" title="Nonaktifkan upacara dan majukan kepulangan 1 JP">
                            Maju 1 JP
                        </button>
                    @else
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="btn btn-primary btn-sm" title="Kembalikan ke jadwal upacara normal">
                            Normal
                        </button>
                    @endif
                </form>
            </div>

            {{-- Row: Pembiasaan Jumat --}}
            <div class="jam-item-row" style="border-top:1px solid var(--border); padding-top:14px;">
                <div style="flex:1; min-width:0;">
                    <div class="d-flex align-center gap-8 mb-4">
                        <strong style="font-size:13px; color:var(--text-primary);">Pembiasaan Pagi (Jumat)</strong>
                        <span class="badge {{ $jumatAdaPembiasaan ? 'badge-success' : 'badge-amber' }}">
                            {{ $jumatAdaPembiasaan ? 'Ada Pembiasaan' : 'Maju 1 JP' }}
                        </span>
                    </div>
                    <div style="font-size:12px; color:var(--text-secondary); line-height:1.4;">
                        @if($jumatAdaPembiasaan)
                            KBM normal (X: <strong style="color:var(--text-primary);">{{ $jamPulangJumatXNormal }}</strong>, XI/XII: <strong style="color:var(--text-primary);">{{ $jamPulangJumatXiNormal }} WIB</strong>)
                        @else
                            Tanpa pembiasaan, maju 30 menit (X: <strong style="color:var(--gold-accent, #f59e0b);">{{ $jamPulangJumatXTanpaPembiasaan }}</strong>, XI/XII: <strong style="color:var(--gold-accent, #f59e0b);">{{ $jamPulangJumatXiTanpaPembiasaan }} WIB</strong>)
                        @endif
                    </div>
                </div>
                <form action="{{ route('admin.jam-sekolah.toggle-pembiasaan') }}" method="POST" style="margin:0; flex-shrink:0;">
                    @csrf
                    @if($jumatAdaPembiasaan)
                        <input type="hidden" name="status" value="0">
                        <button type="submit" class="btn btn-secondary btn-sm" title="Nonaktifkan pembiasaan dan majukan kepulangan 1 JP">
                            Maju 1 JP
                        </button>
                    @else
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="btn btn-primary btn-sm" title="Kembalikan ke jadwal pembiasaan normal">
                            Normal
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- BOX 2: ACARA MENDADAK / PULANG CEPAT --}}
        <div class="jam-box" style="margin-bottom:0;">
            <div class="jam-box-head">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <h3 class="jam-box-title">Pulang Cepat (Acara Khusus)</h3>
                @if($acaraMendadakConfig['aktif'])
                    <span class="badge badge-danger">Aktif · {{ $acaraMendadakConfig['jam_pulang'] }} WIB</span>
                @else
                    <span class="badge badge-gray">Tidak Aktif</span>
                @endif
            </div>

            <form action="{{ route('admin.jam-sekolah.acara-mendadak') }}" method="POST" style="display:flex; flex-direction:column; flex:1;">
                @csrf
                <input type="hidden" name="aktif" value="1">

                <div class="form-row mb-12">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="acara_tanggal">Tanggal Berlaku <span class="req">*</span></label>
                        <input type="date" id="acara_tanggal" name="tanggal" value="{{ old('tanggal', $acaraMendadakConfig['tanggal']) }}" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="acara_jam_pulang">Jam Pulang Cepat <span class="req">*</span></label>
                        <input type="time" id="acara_jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $acaraMendadakConfig['jam_pulang']) }}" class="form-control" required>
                    </div>
                </div>

                <div class="form-row mb-12">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="acara_target">Sasaran Kelas</label>
                        <select id="acara_target" name="target" class="form-control">
                            <option value="semua" {{ $acaraMendadakConfig['target'] === 'semua' ? 'selected' : '' }}>Semua Kelas</option>
                            <option value="x" {{ $acaraMendadakConfig['target'] === 'x' ? 'selected' : '' }}>Kelas X (10)</option>
                            <option value="xi" {{ $acaraMendadakConfig['target'] === 'xi' ? 'selected' : '' }}>Kelas XI (11)</option>
                            <option value="xii" {{ $acaraMendadakConfig['target'] === 'xii' ? 'selected' : '' }}>Kelas XII (12)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="acara_alasan">Keterangan / Alasan <span class="req">*</span></label>
                        <input type="text" id="acara_alasan" name="alasan" value="{{ old('alasan', $acaraMendadakConfig['alasan']) }}" placeholder="Contoh: Rapat Dewan Guru" class="form-control" required>
                    </div>
                </div>

                <div class="d-flex align-center justify-between mt-auto" style="padding-top:12px; border-top:1px solid var(--border); gap:10px; flex-wrap:wrap;">
                    <label class="d-inline-flex align-center gap-6" style="font-size:12px; color:var(--text-secondary); cursor:pointer; margin:0;">
                        <input type="checkbox" name="kirim_notifikasi" value="1" checked style="accent-color:var(--navy-primary);">
                        <span>Kirim notifikasi siaran</span>
                    </label>
                    <div class="d-flex align-center gap-8">
                        @if($acaraMendadakConfig['aktif'])
                            <button type="button" onclick="document.getElementById('formBatalAcara').submit();" class="btn btn-secondary btn-sm" style="color:var(--danger, #ef4444);">
                                Batalkan
                            </button>
                        @endif
                        <button type="submit" class="btn btn-primary btn-sm">
                            {{ $acaraMendadakConfig['aktif'] ? 'Perbarui' : 'Terapkan' }}
                        </button>
                    </div>
                </div>
            </form>

            @if($acaraMendadakConfig['aktif'])
                <form id="formBatalAcara" action="{{ route('admin.jam-sekolah.acara-mendadak') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="aktif" value="0">
                </form>
            @endif
        </div>

    </div>

    {{-- Main Configuration Form --}}
    <form action="{{ route('admin.jam-sekolah.update') }}" method="POST">
        @csrf

        <div class="jam-settings-grid">

            {{-- BLOK 1: JAM OPERASIONAL SEKOLAH (MASUK & PULANG) --}}
            <div class="jam-box">
                <div class="jam-box-head">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <h3 class="jam-box-title">1. Waktu Masuk & Pulang</h3>
                    <span class="jam-box-badge">Utama</span>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label" for="jam_masuk">Jam Masuk Sekolah <span class="req">*</span></label>
                    <input type="time" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $jamMasuk) }}" class="form-control" required>
                    <small class="text-muted">Waktu mulai apel/KBM pagi hari untuk seluruh tingkat.</small>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label" for="jam_pulang">Jam Pulang (Senin — Kamis) <span class="req">*</span></label>
                    <input type="time" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $jamPulang) }}" class="form-control" required>
                    <small class="text-muted">Akhir KBM Senin s.d Kamis (Semua Tingkat).</small>
                </div>

                <div class="form-row mb-8">
                    <div class="form-group">
                        <label class="form-label" for="jam_pulang_jumat_x">Pulang Jumat (Kelas 10) <span class="req">*</span></label>
                        <input type="time" id="jam_pulang_jumat_x" name="jam_pulang_jumat_x" value="{{ old('jam_pulang_jumat_x', $jamPulangJumatX) }}" class="form-control" required>
                        <small class="text-muted">13 JP (Kelas X).</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="jam_pulang_jumat_xi">Pulang Jumat (Kelas 11 & 12) <span class="req">*</span></label>
                        <input type="time" id="jam_pulang_jumat_xi" name="jam_pulang_jumat_xi" value="{{ old('jam_pulang_jumat_xi', $jamPulangJumatXi) }}" class="form-control" required>
                        <small class="text-muted">12 JP (Kelas XI & XII).</small>
                    </div>
                </div>
            </div>

            {{-- BLOK 2: DURASI JP & TOLERANSI MASUK --}}
            <div class="jam-box">
                <div class="jam-box-head">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <h3 class="jam-box-title">2. Durasi JP & Keterlambatan</h3>
                    <span class="jam-box-badge">Durasi</span>
                </div>

                <div class="form-row mb-16">
                    <div class="form-group">
                        <label class="form-label" for="durasi_pelajaran_menit">Durasi 1 JP (Senin–Kamis)</label>
                        <div class="d-flex align-center gap-8">
                            <input type="number" id="durasi_pelajaran_menit" name="durasi_pelajaran_menit" value="{{ old('durasi_pelajaran_menit', $durasiPelajaran) }}" min="1" max="120" required class="form-control" style="width: 100px;">
                            <span class="text-muted fw-bold">menit</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="durasi_pelajaran_jumat_menit">Durasi 1 JP (Jumat)</label>
                        <div class="d-flex align-center gap-8">
                            <input type="number" id="durasi_pelajaran_jumat_menit" name="durasi_pelajaran_jumat_menit" value="{{ old('durasi_pelajaran_jumat_menit', $durasiPelajaranJumat) }}" min="1" max="120" required class="form-control" style="width: 100px;">
                            <span class="text-muted fw-bold">menit</span>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label" for="toleransi_keterlambatan_menit">Toleransi Keterlambatan Masuk</label>
                    <div class="d-flex align-center gap-8">
                        <input type="number" id="toleransi_keterlambatan_menit" name="toleransi_keterlambatan_menit" value="{{ old('toleransi_keterlambatan_menit', $toleransiTerlambat) }}" min="0" max="60" required class="form-control" style="width: 100px;">
                        <span class="text-muted fw-bold">menit</span>
                    </div>
                    <small class="text-muted">Batas menit sebelum presensi guru/siswa otomatis dianggap "Terlambat".</small>
                </div>
            </div>

            {{-- BLOK 3: ATURAN JURNAL GURU & TOLERANSI PIKET --}}
            <div class="jam-box">
                <div class="jam-box-head">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <h3 class="jam-box-title">3. Aturan Jurnal Guru & Piket</h3>
                    <span class="jam-box-badge">Validasi</span>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label" for="batas_waktu_jurnal_menit">Batas Pengisian Jurnal Mengajar</label>
                    <div class="d-flex align-center gap-8">
                        <input type="number" id="batas_waktu_jurnal_menit" name="batas_waktu_jurnal_menit" value="{{ old('batas_waktu_jurnal_menit', $batasWaktuJurnal) }}" min="0" required class="form-control" style="width: 100px;">
                        <span class="text-muted fw-bold">menit</span>
                    </div>
                    <small class="text-muted">Toleransi pengisian jurnal guru setelah jam pelajaran berakhir.</small>
                </div>

                <div class="form-group mb-16">
                    <label class="form-label" for="toleransi_kelas_kosong_menit">Toleransi Kelas Kosong (Piket)</label>
                    <div class="d-flex align-center gap-8">
                        <input type="number" id="toleransi_kelas_kosong_menit" name="toleransi_kelas_kosong_menit" value="{{ old('toleransi_kelas_kosong_menit', $toleransiKelasKosong) }}" min="0" required class="form-control" style="width: 100px;">
                        <span class="text-muted fw-bold">menit</span>
                    </div>
                    <small class="text-muted">Peringatan bagi petugas piket saat kelas belum dihadiri guru.</small>
                </div>
            </div>

        </div>

    {{-- Notice Callout for Friday Grade Rules --}}
    <div class="friday-notice-callout">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <div>
            <div class="friday-notice-title">Ketentuan Khusus Hari Jumat (Perbedaan Tingkat Kelas)</div>
            <p class="friday-notice-desc">
                • <strong>Kelas X (10)</strong>: Melaksanakan pembelajaran hingga <strong>Jam Ke-13 (Pulang {{ $jamPulangJumatX }} WIB)</strong> untuk program Pembiasaan/P5.<br>
                • <strong>Kelas XI (11) & Kelas XII (12)</strong>: Melaksanakan pembelajaran hingga <strong>Jam Ke-12 (Pulang {{ $jamPulangJumatXi }} WIB)</strong>. Banner jam di masing-masing dashboard kelas/guru akan otomatis menyesuaikan tingkat kelas tersebut.
            </p>
        </div>
    </div>

    {{-- TABEL ALOKASI SLOT JAM PELAJARAN --}}
    <div class="jam-tabs-container">
        <div class="jam-tabs-header">
            <div class="jam-tab-pills">
                <button type="button" class="jam-tab-pill active" onclick="switchJamDetailTab('senin_kamis', this)">
                    📅 Senin — Kamis (Jam 1–10)
                </button>
                <button type="button" class="jam-tab-pill" onclick="switchJamDetailTab('jumat_x', this)">
                    🕌 Jumat — Kelas X / 10 (Jam 1–13)
                </button>
                <button type="button" class="jam-tab-pill" onclick="switchJamDetailTab('jumat_xi', this)">
                    🕌 Jumat — Kelas XI & XII (Jam 1–12)
                </button>
            </div>
            <div>
                @if($isCustomSeninKamis || $isCustomJumat)
                    <span class="badge badge-info">Menggunakan Slot Kustom</span>
                @else
                    <span class="badge badge-success">✓ Standar KBM Reguler</span>
                @endif
            </div>
        </div>

        <div class="jam-tab-body">

            {{-- TAB 1: SENIN - KAMIS (SEMUA TINGKAT) --}}
            <div id="tabDetail_senin_kamis" class="jam-tab-pane active">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 13.5px; color: var(--text-secondary); font-weight: 500;">
                        Alokasi Jam Pelajaran Senin — Kamis (1 JP = {{ $durasiPelajaran }} Menit • Total 10 Jam Pelajaran)
                    </span>
                    <span class="badge badge-navy">Berlaku untuk Kelas X, XI, XII</span>
                </div>

                <div class="slot-tbl-wrap">
                    <table class="slot-tbl">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Jam Ke</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                                <th>Keterangan / Aktivitas Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seninKamisSlots as $jam => $slot)
                                <tr>
                                    <td>
                                        <span class="badge-jp">Jam Ke-{{ $jam }}</span>
                                    </td>
                                    <td>
                                        <input type="time" name="slots_senin_kamis[{{ $jam }}][mulai]" value="{{ $slot['waktu_mulai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time" name="slots_senin_kamis[{{ $jam }}][selesai]" value="{{ $slot['waktu_selesai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="text" name="slots_senin_kamis[{{ $jam }}][keterangan]" value="{{ $slot['keterangan'] ?? '' }}" placeholder="KBM Reguler" class="form-control form-control-sm">
                                    </td>
                                </tr>
                                @if(isset($seninKamisIstirahat[$jam]))
                                    <tr class="row-break">
                                        <td>☕ Istirahat</td>
                                        <td colspan="3">
                                            <strong>{{ $seninKamisIstirahat[$jam]['label'] }}</strong> ({{ $seninKamisIstirahat[$jam]['waktu'] }})
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: JUMAT - KELAS X (10) --}}
            <div id="tabDetail_jumat_x" class="jam-tab-pane">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 13.5px; color: var(--text-secondary); font-weight: 500;">
                        Alokasi Jam Pelajaran Jumat Khusus <strong>Kelas X (10)</strong> (1 JP = {{ $durasiPelajaranJumat }} Menit • Total 13 Jam Pelajaran • Pulang {{ $jamPulangJumatX }} WIB)
                    </span>
                    <span class="badge badge-purple" style="background:#8b5cf6; color:#ffffff;">Khusus Kelas X (10)</span>
                </div>

                <div class="slot-tbl-wrap">
                    <table class="slot-tbl">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Jam Ke</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                                <th>Keterangan / Aktivitas Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jumatSlotsX as $jam => $slot)
                                <tr class="{{ (int)$jam == 13 ? 'row-highlight-x' : '' }}">
                                    <td>
                                        <span class="badge-jp {{ (int)$jam == 13 ? 'badge-purple' : '' }}" @if((int)$jam == 13) style="background:#8b5cf6; color:#ffffff;" @endif>
                                            Jam Ke-{{ $jam }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="time" name="slots_jumat[{{ $jam }}][mulai]" value="{{ $slot['waktu_mulai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time" name="slots_jumat[{{ $jam }}][selesai]" value="{{ $slot['waktu_selesai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="text" name="slots_jumat[{{ $jam }}][keterangan]" value="{{ $slot['keterangan'] ?? '' }}" placeholder="{{ (int)$jam == 13 ? 'Khusus Kelas X (Pembiasaan/P5)' : 'KBM Jumat' }}" class="form-control form-control-sm">
                                    </td>
                                </tr>
                                @if(isset($jumatIstirahat[$jam]))
                                    <tr class="row-break">
                                        <td>🕌 Istirahat</td>
                                        <td colspan="3">
                                            <strong>{{ $jumatIstirahat[$jam]['label'] }}</strong> ({{ $jumatIstirahat[$jam]['waktu'] }})
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: JUMAT - KELAS XI & XII (11 & 12) --}}
            <div id="tabDetail_jumat_xi" class="jam-tab-pane">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 13.5px; color: var(--text-secondary); font-weight: 500;">
                        Alokasi Jam Pelajaran Jumat Khusus <strong>Kelas XI (11) & XII (12)</strong> (1 JP = {{ $durasiPelajaranJumat }} Menit • Total 12 Jam Pelajaran • Pulang {{ $jamPulangJumatXi }} WIB)
                    </span>
                    <span class="badge badge-amber" style="background:#f59e0b; color:#ffffff;">Kelas XI (11) & XII (12)</span>
                </div>

                <div class="slot-tbl-wrap">
                    <table class="slot-tbl">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Jam Ke</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                                <th>Keterangan / Aktivitas Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jumatSlotsXi as $jam => $slot)
                                <tr>
                                    <td>
                                        <span class="badge-jp">Jam Ke-{{ $jam }}</span>
                                    </td>
                                    <td>
                                        <input type="time" disabled value="{{ $slot['waktu_mulai'] }}" class="form-control form-control-sm" style="width: 130px; opacity:0.85;">
                                    </td>
                                    <td>
                                        <input type="time" disabled value="{{ $slot['waktu_selesai'] }}" class="form-control form-control-sm" style="width: 130px; opacity:0.85;">
                                    </td>
                                    <td>
                                        <input type="text" disabled value="{{ $slot['keterangan'] ?? '' }}" placeholder="Mengikuti konfigurasi Jumat (Jam 1–12)" class="form-control form-control-sm" style="opacity:0.85;">
                                    </td>
                                </tr>
                                @if(isset($jumatIstirahat[$jam]))
                                    <tr class="row-break">
                                        <td>🕌 Istirahat</td>
                                        <td colspan="3">
                                            <strong>{{ $jumatIstirahat[$jam]['label'] }}</strong> ({{ $jumatIstirahat[$jam]['waktu'] }})
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SECTION: PENGATURAN JAM ISTIRAHAT (TERPISAH) --}}
    <div class="jam-tabs-container" style="margin-bottom: 24px;">
        <div class="jam-tabs-header">
            <div class="jam-tab-pills">
                <button type="button" class="jam-tab-pill active" onclick="switchIstirahatTab('ist_senin_kamis', this)">
                    ☕ Istirahat Senin — Kamis
                </button>
                <button type="button" class="jam-tab-pill" onclick="switchIstirahatTab('ist_jumat', this)">
                    🕌 Istirahat Jumat
                </button>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">Pengaturan Jam Istirahat</span>
                @if($isCustomIstirahatSeninKamis || $isCustomIstirahatJumat)
                    <span class="badge badge-info">Kustom</span>
                @else
                    <span class="badge badge-success">✓ Standar</span>
                @endif
            </div>
        </div>

        <div class="jam-tab-body">

            {{-- TAB ISTIRAHAT 1: SENIN - KAMIS --}}
            <div id="tabDetail_ist_senin_kamis" class="jam-tab-pane active">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 13.5px; color: var(--text-secondary); font-weight: 500;">
                        Jam Istirahat Senin — Kamis — disisipkan setelah Jam KBM yang ditentukan
                    </span>
                    <span class="badge badge-navy">Berlaku untuk Kelas X, XI, XII</span>
                </div>

                <div class="slot-tbl-wrap">
                    <table class="slot-tbl">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Nama Istirahat</th>
                                <th style="width: 160px;">Setelah Jam KBM Ke-</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seninKamisIstirahat as $afterJam => $ist)
                                <tr class="row-break">
                                    <td>
                                        <input type="text"
                                            name="istirahat_senin_kamis[{{ $afterJam }}][label]"
                                            value="{{ $ist['label'] }}"
                                            class="form-control form-control-sm"
                                            placeholder="Nama istirahat">
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="istirahat_senin_kamis[{{ $afterJam }}][setelah_jam]"
                                            value="{{ $afterJam }}"
                                            min="1" max="10"
                                            class="form-control form-control-sm"
                                            style="width: 90px;">
                                    </td>
                                    <td>
                                        <input type="time"
                                            name="istirahat_senin_kamis[{{ $afterJam }}][mulai]"
                                            value="{{ $ist['waktu_mulai'] }}"
                                            class="form-control form-control-sm"
                                            style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time"
                                            name="istirahat_senin_kamis[{{ $afterJam }}][selesai]"
                                            value="{{ $ist['waktu_selesai'] }}"
                                            class="form-control form-control-sm"
                                            style="width: 130px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 10px;">
                    <small class="text-muted">
                        💡 <strong>Setelah Jam KBM Ke-</strong>: jam pelajaran sebelum istirahat disisipkan. Contoh: nilai <code>4</code> = istirahat muncul setelah Jam Ke-4 selesai.
                    </small>
                </div>
            </div>

            {{-- TAB ISTIRAHAT 2: JUMAT --}}
            <div id="tabDetail_ist_jumat" class="jam-tab-pane">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 13.5px; color: var(--text-secondary); font-weight: 500;">
                        Jam Istirahat Hari Jumat — berlaku untuk semua tingkat kelas (X, XI & XII)
                    </span>
                    <span class="badge" style="background:#f59e0b; color:#ffffff;">Jumat</span>
                </div>

                <div class="slot-tbl-wrap">
                    <table class="slot-tbl">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Nama Istirahat</th>
                                <th style="width: 160px;">Setelah Jam KBM Ke-</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jumatIstirahat as $afterJam => $ist)
                                <tr class="row-break">
                                    <td>
                                        <input type="text"
                                            name="istirahat_jumat[{{ $afterJam }}][label]"
                                            value="{{ $ist['label'] }}"
                                            class="form-control form-control-sm"
                                            placeholder="Nama istirahat">
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="istirahat_jumat[{{ $afterJam }}][setelah_jam]"
                                            value="{{ $afterJam }}"
                                            min="1" max="13"
                                            class="form-control form-control-sm"
                                            style="width: 90px;">
                                    </td>
                                    <td>
                                        <input type="time"
                                            name="istirahat_jumat[{{ $afterJam }}][mulai]"
                                            value="{{ $ist['waktu_mulai'] }}"
                                            class="form-control form-control-sm"
                                            style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time"
                                            name="istirahat_jumat[{{ $afterJam }}][selesai]"
                                            value="{{ $ist['waktu_selesai'] }}"
                                            class="form-control form-control-sm"
                                            style="width: 130px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 10px;">
                    <small class="text-muted">
                        💡 <strong>Setelah Jam KBM Ke-</strong>: jam pelajaran sebelum istirahat disisipkan. Contoh: nilai <code>8</code> = Solat Jumat muncul setelah Jam Ke-8 selesai.
                    </small>
                </div>
            </div>

        </div>
    </div>

    {{-- TOMBOL SUBMIT --}}
    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 40px;">
        <button type="submit" class="btn btn-primary btn-lg" style="padding: 12px 28px; font-weight: 700; font-size: 15px; box-shadow: var(--shadow-md);">
            SIMPAN PENGATURAN JAM SEKOLAH
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">
            Batal
        </a>
    </div>

    </form>

</div>

<script>
function switchJamDetailTab(tabKey, btnEl) {
    document.querySelectorAll('.jam-tab-pill').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.jam-tab-pane').forEach(p => p.classList.remove('active'));

    if (btnEl) btnEl.classList.add('active');
    const targetPane = document.getElementById('tabDetail_' + tabKey);
    if (targetPane) targetPane.classList.add('active');
}

function switchIstirahatTab(tabKey, btnEl) {
    // Hanya toggle tab dalam container istirahat
    const container = btnEl.closest('.jam-tabs-container');
    container.querySelectorAll('.jam-tab-pill').forEach(b => b.classList.remove('active'));
    container.querySelectorAll('.jam-tab-pane').forEach(p => p.classList.remove('active'));

    if (btnEl) btnEl.classList.add('active');
    const targetPane = document.getElementById('tabDetail_' + tabKey);
    if (targetPane) targetPane.classList.add('active');
}
</script>
@endsection