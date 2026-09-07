@extends('layouts.app')

@section('title', 'Profil Akun — Jurnal Sekolah')
@section('page-title', 'Profil Akun')

@section('content')
<style>
/* ==========================================================================
   PROFIL AKUN & PENGATURAN — MODERN & AESTHETIC DESIGN
   ========================================================================== */
.profile-wrapper {
    max-width: 1060px;
    margin: 0 auto;
}

/* Hero Header Card */
.profile-hero-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 24px;
    transition: background-color .3s, border-color .3s;
}

.profile-hero-banner {
    height: 140px;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #3b82f6 100%);
    position: relative;
    overflow: hidden;
}

.profile-hero-pattern {
    position: absolute;
    inset: 0;
    opacity: .25;
    background-image: 
        radial-gradient(circle at 15% 30%, rgba(255,255,255,.3) 0, transparent 45%),
        radial-gradient(circle at 85% 65%, rgba(255,255,255,.2) 0, transparent 40%),
        linear-gradient(45deg, transparent 48%, rgba(255,255,255,.1) 50%, transparent 52%);
    background-size: cover;
}

.profile-hero-badge {
    position: absolute;
    top: 16px;
    right: 20px;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: #e0e7ff;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .5px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Hero Body / Avatar & Info */
.profile-hero-body {
    padding: 0 28px 24px;
    position: relative;
}

.profile-hero-top {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: -65px;
    margin-bottom: 18px;
}

/* Avatar Container with Sleek Camera Button */
.profile-avatar-box {
    position: relative;
    width: 124px;
    height: 124px;
    border-radius: 50%;
    border: 4px solid var(--bg-card);
    background: var(--bg-card);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25);
    flex-shrink: 0;
}

.profile-avatar-img,
.profile-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar-placeholder {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #ffffff;
    font-size: 44px;
    font-weight: 700;
    text-transform: uppercase;
    user-select: none;
}

/* Camera Action Button Badge */
.avatar-camera-btn {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--navy-primary);
    color: #ffffff;
    border: 3px solid var(--bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
    transition: transform .2s ease, background-color .2s ease;
}

.avatar-camera-btn:hover {
    background: #2563eb;
    transform: scale(1.08);
}

.avatar-camera-btn svg {
    width: 18px;
    height: 18px;
}

/* Upload Prompt / Pending Actions Pill */
.avatar-upload-actions {
    display: none;
    align-items: center;
    gap: 10px;
    background: var(--badge-info-bg);
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 8px 16px;
    border-radius: 999px;
    margin-top: 14px;
    animation: fadeIn .3s ease;
}

.avatar-upload-actions.show {
    display: inline-flex;
}

.avatar-upload-text {
    font-size: 13px;
    color: var(--text-primary);
    font-weight: 500;
}

.avatar-upload-btns {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Identity Info Typography */
.profile-meta-main {
    flex: 1;
    min-width: 250px;
    padding-top: 8px;
}

.profile-name-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 6px;
}

.profile-full-name {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1.2;
}

.profile-role-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    background: var(--badge-navy-bg);
    color: var(--badge-navy-text);
}

.profile-pills-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    color: var(--text-secondary);
    font-size: 13px;
    margin-top: 6px;
}

.profile-pill-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-card-header);
    border: 1px solid var(--border);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
}

.profile-pill-item svg {
    width: 14px;
    height: 14px;
    color: var(--text-muted);
}

/* Modern Tab Navigation */
.profile-tabs-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid var(--border);
    padding-bottom: 2px;
    overflow-x: auto;
}

.profile-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s ease;
    white-space: nowrap;
    position: relative;
    top: 2px;
}

.profile-tab-btn:hover {
    color: var(--navy-primary);
    background: var(--badge-navy-bg);
}

.profile-tab-btn.active {
    color: var(--navy-primary);
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-bottom: 2px solid var(--bg-card);
    box-shadow: 0 -2px 6px rgba(0,0,0,0.03);
}

[data-theme="dark"] .profile-tab-btn.active {
    color: #60a5fa;
    background: var(--bg-card);
    border-color: var(--border);
    border-bottom: 2px solid var(--bg-card);
}

.profile-tab-btn svg {
    width: 16px;
    height: 16px;
}

/* Tab Panels */
.profile-tab-content {
    display: none;
    animation: fadeIn .25s ease;
}

.profile-tab-content.active {
    display: block;
}

/* Cards & Layout */
.profile-section-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
    transition: background-color .3s, border-color .3s;
}

.profile-card-head {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card-header);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.profile-card-head-title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-card-body {
    padding: 24px;
}

/* Information Tiles Grid */
.info-tiles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}

.info-tile {
    background: var(--bg-card-header);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: transform .15s ease, border-color .2s;
}

.info-tile:hover {
    border-color: #93c5fd;
    transform: translateY(-1px);
}

.info-tile-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: var(--badge-navy-bg);
    color: var(--navy-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

[data-theme="dark"] .info-tile-icon {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
}

.info-tile-icon svg {
    width: 18px;
    height: 18px;
}

.info-tile-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.info-tile-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--text-muted);
    margin-bottom: 3px;
}

.info-tile-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
}

/* Form Styling within Settings / Security */
.settings-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.settings-subgroup {
    background: var(--bg-card-header);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 18px;
    margin-bottom: 20px;
}

.settings-subgroup-title {
    margin: 0 0 14px;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.preference-check-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: background-color .15s, border-color .15s;
}

.preference-check-item:hover {
    border-color: #93c5fd;
}

.preference-check-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--navy-primary);
}

.preference-check-text {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-primary);
}

/* Password Toggle Wrapper */
.password-field-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-field-wrapper input {
    padding-right: 42px !important;
}

.password-toggle-btn {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color .2s;
}

.password-toggle-btn:hover {
    color: var(--text-primary);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-hero-banner { height: 110px; }
    .profile-avatar-box { width: 100px; height: 100px; margin-top: -50px; }
    .profile-avatar-placeholder { font-size: 34px; }
    .avatar-camera-btn { width: 32px; height: 32px; bottom: 0; right: 0; }
    .avatar-camera-btn svg { width: 14px; height: 14px; }
    .profile-hero-top { margin-top: -50px; }
    .profile-full-name { font-size: 20px; }
    .profile-hero-body { padding: 0 16px 20px; }
    .info-tiles-grid { grid-template-columns: 1fr; }
}
</style>

<div class="profile-wrapper">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- ====================================================== --}}
    {{-- HERO HEADER CARD (AVATAR, NAME, ROLE, DIRECT PHOTO EDIT) --}}
    {{-- ====================================================== --}}
    <div class="profile-hero-card">
        <div class="profile-hero-banner">
            <div class="profile-hero-pattern"></div>
            <div class="profile-hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Tahun Ajaran: {{ $tahunAktif->tahun_pelajaran ?? 'Aktif' }}
            </div>
        </div>

        <div class="profile-hero-body">
            <div class="profile-hero-top">
                {{-- AVATAR DENGAN TOMBOL KAMERA OVERLAY --}}
                <div class="profile-avatar-box" id="avatarBoxWrap">
                    @if($user->foto_profil_url)
                        <img id="avatarImgPreview" class="profile-avatar-img" src="{{ $user->foto_profil_url }}" alt="{{ $user->nama }}">
                    @else
                        <div id="avatarPlaceholderBox" class="profile-avatar-placeholder">
                            {{ strtoupper(substr($user->nama ?? $user->username, 0, 1)) }}
                        </div>
                    @endif

                    {{-- Tombol Kamera Overlay --}}
                    <label for="foto_profil_input" class="avatar-camera-btn" title="Klik untuk mengganti foto profil">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </label>
                </div>

                {{-- FORM UPLOAD FOTO TERSEMBUNYI --}}
                <form id="fotoProfilForm" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" style="display:none;">
                    @csrf
                    <input type="file" id="foto_profil_input" name="foto_profil" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                           onchange="handleFotoSelected(this)">
                </form>

                {{-- IDENTITAS & STATUS --}}
                <div class="profile-meta-main">
                    <div class="profile-name-row">
                        <h1 class="profile-full-name">{{ $user->nama ?? $user->username }}</h1>
                        <span class="profile-role-tag">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                    </div>

                    <div class="profile-pills-row">
                        <span class="profile-pill-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            {{ '@' . $user->username }}
                        </span>
                        @if(!empty($user->no_hp))
                            <span class="profile-pill-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                {{ $user->no_hp }}
                            </span>
                        @endif
                        <span class="badge badge-success" style="font-size:11px;">● Akun Aktif</span>
                    </div>
                </div>
            </div>

            {{-- TOOLBAR KONFIRMASI UPLOAD FOTO (MUNCUL OTOMATIS SAAT GAMBAR DIPILIH) --}}
            <div id="avatarUploadActions" class="avatar-upload-actions">
                <span class="avatar-upload-text">
                    📸 Foto baru dipilih: <strong id="selectedPhotoName">foto.jpg</strong>
                </span>
                <div class="avatar-upload-btns">
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('fotoProfilForm').submit()">
                        Simpan Foto
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="resetFotoPreview()">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- MODERN TAB NAVIGATION --}}
    {{-- ====================================================== --}}
    <div class="profile-tabs-bar">
        <button type="button" class="profile-tab-btn {{ $activeTab === 'profil' ? 'active' : '' }}" onclick="switchProfileTab('profil', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Informasi Profil
        </button>
        <button type="button" class="profile-tab-btn {{ $activeTab === 'keamanan' ? 'active' : '' }}" onclick="switchProfileTab('keamanan', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Keamanan Akun
        </button>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB 1: INFORMASI PROFIL LENGKAP --}}
    {{-- ====================================================== --}}
    <div id="tabContent_profil" class="profile-tab-content {{ $activeTab === 'profil' ? 'active' : '' }}">
        <div class="profile-section-card">
            <div class="profile-card-head">
                <h3 class="profile-card-head-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Biodata & Detail Akun
                </h3>
            </div>
            <div class="profile-card-body">
                <div class="info-tiles-grid">
                    {{-- Nama Lengkap --}}
                    <div class="info-tile">
                        <div class="info-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="info-tile-content">
                            <span class="info-tile-label">Nama Lengkap</span>
                            <span class="info-tile-value">{{ $user->nama ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="info-tile">
                        <div class="info-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path></svg>
                        </div>
                        <div class="info-tile-content">
                            <span class="info-tile-label">Username Akun</span>
                            <span class="info-tile-value">{{ $user->username }}</span>
                        </div>
                    </div>

                    {{-- Peran Sistem --}}
                    <div class="info-tile">
                        <div class="info-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div class="info-tile-content">
                            <span class="info-tile-label">Hak Akses / Role</span>
                            <span class="info-tile-value">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                        </div>
                    </div>

                    {{-- No. HP / Telepon --}}
                    <div class="info-tile">
                        <div class="info-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="info-tile-content">
                            <span class="info-tile-label">Nomor WhatsApp / HP</span>
                            <span class="info-tile-value">{{ $user->no_hp ?? ($detailGuru->no_telp ?? '-') }}</span>
                        </div>
                    </div>

                    {{-- DETAIL GURU (JIKA USER ADALAH GURU) --}}
                    @if($detailGuru)
                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">NIP Guru</span>
                                <span class="info-tile-value">{{ $detailGuru->nip ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">Bidang Studi / Mapel</span>
                                <span class="info-tile-value">{{ $detailGuru->bidang_studi ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">Wali Kelas</span>
                                <span class="info-tile-value">
                                    {{ $detailGuru->kelasWali ? 'Kelas ' . $detailGuru->kelasWali->nama_kelas : 'Bukan Wali Kelas' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- DETAIL SISWA (JIKA USER ADALAH SISWA) --}}
                    @if($detailSiswa)
                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">NISN / NIS</span>
                                <span class="info-tile-value">{{ $detailSiswa->nisn ?? ($detailSiswa->nis ?? '-') }}</span>
                            </div>
                        </div>

                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">Kelas Saat Ini</span>
                                <span class="info-tile-value">{{ $detailSiswa->kelas->nama_kelas ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-tile">
                            <div class="info-tile-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div class="info-tile-content">
                                <span class="info-tile-label">Tempat & Tgl Lahir</span>
                                <span class="info-tile-value">
                                    {{ $detailSiswa->tempat_lahir ?? '-' }}, 
                                    {{ $detailSiswa->tanggal_lahir ? date('d/m/Y', strtotime($detailSiswa->tanggal_lahir)) : '-' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Tahun Pelajaran Aktif --}}
                    <div class="info-tile">
                        <div class="info-tile-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        </div>
                        <div class="info-tile-content">
                            <span class="info-tile-label">Tahun Ajaran Aktif</span>
                            <span class="info-tile-value">{{ $tahunAktif->tahun_pelajaran ?? '-' }} ({{ $tahunAktif->semester ?? '-' }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB 2: KEAMANAN AKUN (UBAH USERNAME & PASSWORD) --}}
    {{-- ====================================================== --}}
    <div id="tabContent_keamanan" class="profile-tab-content {{ $activeTab === 'keamanan' ? 'active' : '' }}">
        <div class="grid-2" style="gap:24px;">

            {{-- FORM UBAH USERNAME --}}
            <div class="profile-section-card">
                <div class="profile-card-head">
                    <h3 class="profile-card-head-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Ubah Username
                    </h3>
                </div>
                <div class="profile-card-body">
                    <form action="{{ route('profil.username') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="username">Username Baru <span class="req">*</span></label>
                            <input type="text" id="username" name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   class="form-control" required autocomplete="username">
                            <small class="text-muted" style="display:block; margin-top:6px;">
                                Username digunakan untuk masuk ke dalam aplikasi sistem.
                            </small>
                        </div>
                        @error('username')
                            <div class="alert alert-danger" style="padding:8px 12px; font-size:13px; margin-top:8px;">
                                {{ $message }}
                            </div>
                        @enderror
                        <button type="submit" class="btn btn-primary mt-16">
                            💾 Simpan Username
                        </button>
                    </form>
                </div>
            </div>

            {{-- FORM UBAH PASSWORD --}}
            <div class="profile-section-card">
                <div class="profile-card-head">
                    <h3 class="profile-card-head-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Ganti Password
                    </h3>
                </div>
                <div class="profile-card-body">
                    <form action="{{ route('profil.password') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="password_lama">Password Saat Ini <span class="req">*</span></label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password_lama" name="password_lama" class="form-control" required autocomplete="current-password">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_lama', this)" title="Lihat/Sembunyikan password">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_baru">Password Baru <span class="req">*</span></label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password_baru" name="password_baru" class="form-control" required minlength="6" autocomplete="new-password">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_baru', this)" title="Lihat/Sembunyikan password">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            <small class="text-muted" style="display:block; margin-top:4px;">Minimal 6 karakter.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_baru_confirmation">Ulangi Password Baru <span class="req">*</span></label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation" class="form-control" required minlength="6" autocomplete="new-password">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_baru_confirmation', this)" title="Lihat/Sembunyikan password">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        @error('password_lama')
                            <div class="alert alert-danger" style="padding:8px 12px; font-size:13px; margin-top:8px;">{{ $message }}</div>
                        @enderror
                        @error('password_baru')
                            <div class="alert alert-danger" style="padding:8px 12px; font-size:13px; margin-top:8px;">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-amber mt-16" style="background:#d97706; color:#ffffff; font-weight:600;">
                            🔑 Perbarui Password
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- SCRIPT INTERAKTIF PROFIL, PREVIEW FOTO & TAB CONTROL --}}
<script>
// Tab Switching
function switchProfileTab(tabName, btnElement) {
    // Buttons
    document.querySelectorAll('.profile-tab-btn').forEach(btn => btn.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Panels
    document.querySelectorAll('.profile-tab-content').forEach(panel => panel.classList.remove('active'));
    const targetPanel = document.getElementById('tabContent_' + tabName);
    if (targetPanel) {
        targetPanel.classList.add('active');
    }

    // Update URL hash without scroll jump
    history.replaceState(null, null, '?tab=' + tabName);
}

// Handle Photo File Selection and Real-time Avatar Preview
let originalAvatarContent = null;

function handleFotoSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Cek ukuran file (maks 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto terlalu besar. Maksimal 2MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarWrap = document.getElementById('avatarBoxWrap');
            if (!originalAvatarContent) {
                originalAvatarContent = avatarWrap.innerHTML;
            }

            let imgPreview = document.getElementById('avatarImgPreview');
            const placeholder = document.getElementById('avatarPlaceholderBox');

            if (placeholder) {
                placeholder.style.display = 'none';
            }

            if (imgPreview) {
                imgPreview.src = e.target.result;
            } else {
                imgPreview = document.createElement('img');
                imgPreview.id = 'avatarImgPreview';
                imgPreview.className = 'profile-avatar-img';
                imgPreview.src = e.target.result;
                imgPreview.alt = 'Foto Profil Baru';
                avatarWrap.insertBefore(imgPreview, avatarWrap.firstChild);
            }

            // Tampilkan toolbar konfirmasi foto
            const nameEl = document.getElementById('selectedPhotoName');
            if (nameEl) nameEl.textContent = file.name;
            const actionsBar = document.getElementById('avatarUploadActions');
            if (actionsBar) actionsBar.classList.add('show');
        };
        reader.readAsDataURL(file);
    }
}

function resetFotoPreview() {
    const input = document.getElementById('foto_profil_input');
    if (input) input.value = '';

    const avatarWrap = document.getElementById('avatarBoxWrap');
    if (originalAvatarContent && avatarWrap) {
        avatarWrap.innerHTML = originalAvatarContent;
        originalAvatarContent = null;
    }

    const actionsBar = document.getElementById('avatarUploadActions');
    if (actionsBar) actionsBar.classList.remove('show');
}

// Toggle Show/Hide Password
function togglePasswordVisibility(inputId, btn) {
    const field = document.getElementById(inputId);
    if (!field) return;

    if (field.type === 'password') {
        field.type = 'text';
        btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        field.type = 'password';
        btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}

// Auto-switch tab on page load if errors exist
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->has('username') || $errors->has('password_lama') || $errors->has('password_baru') || request('tab') === 'keamanan')
        const keamananBtn = document.querySelectorAll('.profile-tab-btn')[1];
        if (keamananBtn) switchProfileTab('keamanan', keamananBtn);
    @endif
});
</script>
@endsection
