@extends('layouts.guest')

@section('title', 'Login — Jurnal Sekolah')

@section('content')
<style>
/* Cegah scroll di halaman login */
html, body {
    overflow: hidden !important;
    height: 100% !important;
    max-height: 100vh !important;
}

/* Hilangkan efek kotak/outline saat hover, klik, dan aktif pada tombol icon mata */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear,
.sso-input-pill input::-ms-reveal,
.sso-input-pill input::-ms-clear {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}
.sso-eye-btn,
.sso-eye-btn:hover,
.sso-eye-btn:focus,
.sso-eye-btn:focus-visible,
.sso-eye-btn:active {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    -webkit-tap-highlight-color: transparent !important;
}
.sso-eye-btn:hover,
.sso-eye-btn:focus,
.sso-eye-btn:active {
    color: #475569 !important;
}

/* ===== ANIMASI BINTANG DI ANGKASA ===== */
#starCanvas {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    display: block;
    pointer-events: none;
    z-index: 0;
}

/* ===== FITUR TRANSISI HALUS & RINGAN SSO (GPU ACCELERATED, ANTI-LAG) ===== */
.sso-container {
    transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                max-width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                min-height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                border-radius 0.38s cubic-bezier(0.16, 1, 0.3, 1) !important;
    will-change: width, height, max-width;
    transform: translateZ(0);
    backface-visibility: hidden;
}

/* Clip konten agar tetap ikut rounded corner setelah overflow:visible di container */
.sso-left {
    border-radius: 24px 0 0 24px;
    overflow: hidden;
    transform: translateZ(0);
    transition: padding 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                border-radius 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.sso-right {
    border-radius: 0 24px 24px 0;
    overflow: visible;
    transform: translateZ(0);
    transition: flex 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                max-width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                padding 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.25s ease 0.08s !important;
    will-change: flex, max-width, opacity;
}

/* Tombol Minimize di Pojok Kanan Atas Kotak */
.sso-close-btn {
    position: absolute !important;
    top: 12px !important;
    right: 14px !important;
    z-index: 50;
    width: auto !important;
    height: auto !important;
    background: transparent !important;
    border: none !important;
    color: rgba(147, 197, 253, 0.85);
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 4px;
    outline: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    transition: color 0.2s ease, opacity 0.2s ease;
    opacity: 0.8;
    flex-shrink: 0;
    flex-grow: 0;
}

.sso-close-btn:hover {
    background: transparent;
    color: #ffffff;
    opacity: 1;
    box-shadow: none;
    transform: none;
}

.sso-close-btn:active {
    opacity: 0.6;
    transform: none;
}

/* Sembunyikan tombol minimize saat minimized — "Buka Form Login" sudah menangani restore */
.sso-container.sso-minimized .sso-close-btn {
    display: none !important;
}

/* Light mode */
[data-theme="light"] .sso-close-btn {
    color: rgba(30, 58, 138, 0.6);
    background: transparent;
    box-shadow: none;
}

[data-theme="light"] .sso-close-btn:hover {
    color: #1e3a8a;
    background: transparent;
    box-shadow: none;
}

/* Tombol Munculkan Form Login saat Minimized */
.sso-restore-action-box {
    display: none;
    margin-top: 22px;
}

.sso-container.sso-minimized .sso-restore-action-box {
    display: flex;
    justify-content: center;
}

.sso-restore-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: 50px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: 1.5px solid rgba(147, 197, 253, 0.5);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.3px;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(29, 78, 216, 0.45);
    transition: all 0.25s ease;
    outline: none !important;
}

.sso-restore-btn:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.6);
}

.sso-restore-btn:active {
    transform: translateY(0);
}

/* Animasi Wave halus */
.sso-wave-svg {
    transition: opacity 0.3s ease;
}

.sso-container.sso-minimized .sso-wave-svg {
    opacity: 0 !important;
    pointer-events: none !important;
}

/* KETIKA MINIMIZE AKTIF — KOTAK JADI PERSEGI (SQUARE) SEMPURNA */
.sso-container.sso-minimized {
    width: min(420px, 86vh, 92vw) !important;
    height: min(420px, 86vh, 92vw) !important;
    max-width: min(420px, 86vh, 92vw) !important;
    max-height: min(420px, 86vh, 92vw) !important;
    min-height: unset !important;
    aspect-ratio: 1 / 1 !important;
    margin: auto !important;
    background-color: #0b1329 !important;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.08) !important;
    border-radius: 28px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
}

[data-theme="light"] .sso-container.sso-minimized {
    background-color: #ffffff !important;
    box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
}

/* Sembunyikan panel kanan saat minimized secara halus tanpa display:none agar tidak ngelag */
.sso-container.sso-minimized .sso-right {
    flex: 0 0 0px !important;
    max-width: 0 !important;
    width: 0 !important;
    height: 0 !important;
    max-height: 0 !important;
    min-width: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    opacity: 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
    visibility: hidden !important;
    transition: flex 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                max-width 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.15s ease,
                padding 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* Panel kiri saat minimized — isi penuh kotak persegi, terpusat rapi dan seimbang */
.sso-container.sso-minimized .sso-left {
    flex: 1 1 100% !important;
    width: 100% !important;
    height: 100% !important;
    max-height: 100% !important;
    align-items: center !important;
    text-align: center !important;
    padding: 28px 24px !important;
    justify-content: center !important;
    gap: 12px !important;
    background-color: #0b1329 !important;
    border-radius: 28px !important;
    box-sizing: border-box !important;
    display: flex !important;
    flex-direction: column !important;
}

[data-theme="light"] .sso-container.sso-minimized .sso-left {
    background-color: #ffffff !important;
}

.sso-container.sso-minimized .sso-brand {
    justify-content: center !important;
    text-align: center !important;
    margin-bottom: 0 !important;
    gap: 12px !important;
}

.sso-container.sso-minimized .sso-welcome-box {
    margin: 0 auto !important;
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 0 !important;
}

.sso-container.sso-minimized .sso-welcome-title {
    font-size: 22px !important;
    font-weight: 800 !important;
    margin-bottom: 2px !important;
    color: #3b82f6 !important;
}

.sso-container.sso-minimized .sso-welcome-desc {
    margin: 0 auto !important;
    text-align: center !important;
    max-width: 300px !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    opacity: 0.8 !important;
}

.sso-container.sso-minimized .sso-restore-action-box {
    margin-top: 10px !important;
}

.sso-container.sso-minimized .sso-left-footer {
    display: none !important;
}

/* ===== PASTIKAN TULISAN / BADGE BACKGROUND TETAP TAMPIL SEMPURNA ===== */
.login-page {
    height: 100vh !important;
    min-height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}

.login-aesthetic-shapes {
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
}

/* Posisi badge background agar tidak tertutup kotak dan tidak terpotong di tepi layar */
.shape-badge.badge-accurate {
    top: 6% !important;
    left: 20% !important;
}
.shape-badge.badge-direct-alert {
    top: 6% !important;
    right: 20% !important;
}
.shape-badge.badge-discipline {
    bottom: 6% !important;
    left: 20% !important;
}
.shape-badge.badge-transparent {
    bottom: 6% !important;
    right: 20% !important;
}
.shape-badge.badge-recap {
    bottom: 6% !important;
    left: 4% !important;
}
.shape-badge.badge-terverifikasi {
    bottom: 6% !important;
    right: 4% !important;
}

/* Animasi meteor jatuh vertikal ke samping */
.shooting-star {
    display: block !important;
}
</style>

<!-- Theme Toggle Button (Icon Matahari / Bulan) di Luar Container agar tidak ikut hilang saat minimize -->
<button type="button" id="themeToggleBtn" class="theme-toggle-btn sso-theme-btn-corner" aria-label="Toggle Mode Gelap/Terang" title="Ganti Mode Gelap / Terang">
    <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;">
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    </svg>
    <svg class="moon-icon" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>
</button>

<div class="sso-container {{ ($errors->any() || session('error') || session('success') || session('info') || old('username') || request()->has('form') || request('open')) ? '' : 'sso-minimized' }}" id="ssoContainer">

    <!-- LEFT SIDE: Branding & Welcome -->
    <div class="sso-left">
        <div class="sso-brand">
            <div class="sso-brand-icon" style="background: #ffffff; padding: 4px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.25);">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Jurnal Sekolah" style="width: 100%; height: 100%; object-fit: contain; display: block;">
            </div>
            <div class="sso-brand-text">
                <span class="brand-name">Jurnal Sekolah</span>
                <span class="brand-tag">SISTEM KBM</span>
            </div>
        </div>

        <div class="sso-welcome-box">
            <h1 class="sso-welcome-title">Selamat Datang!</h1>
            <p class="sso-welcome-desc">Silakan masukkan Username, NIP, atau NISN beserta password Anda untuk masuk ke sistem.</p>
            <div class="sso-restore-action-box">
                <button type="button" id="toggleRestoreBtn" class="sso-restore-btn" aria-label="Buka Form Login" title="Buka Form Login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="11 17 6 12 11 7"></polyline>
                        <polyline points="18 17 13 12 18 7"></polyline>
                    </svg>
                    <span>Buka Form Login</span>
                </button>
            </div>
        </div>

        <div class="sso-left-footer">
            <span>&copy; {{ date('Y') }} Jurnal Sekolah. Seluruh hak cipta dilindungi.</span>
        </div>
    </div>

    <!-- RIGHT SIDE: Curved Wave Form Portal -->
    <div class="sso-right">

        <!-- Tombol Minimize di Pojok Kanan Atas Kotak -->
        <button type="button" id="toggleMinimizeBtn" class="sso-close-btn" aria-label="Minimize Form Login" title="Minimize Form Login">
            <svg width="16" height="3" viewBox="0 0 16 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="1" y1="1.5" x2="15" y2="1.5"></line>
            </svg>
        </button>

        <!-- Canvas Animasi Bintang di Angkasa -->
        <canvas id="starCanvas" aria-hidden="true"></canvas>

        <!-- Animated 3-Layered Wave Divider (Seamlessly Blended with Right Panel) -->
        <svg class="sso-wave-svg" viewBox="0 0 80 600" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="waveFrontGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#1d4ed8"/>
                    <stop offset="45%" stop-color="#1e3a8a"/>
                    <stop offset="100%" stop-color="#0f172a"/>
                </linearGradient>
                <linearGradient id="waveFrontGradLight" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#1d4ed8"/>
                    <stop offset="50%" stop-color="#2563eb"/>
                    <stop offset="100%" stop-color="#1e40af"/>
                </linearGradient>
                <clipPath id="waveClip">
                    <rect x="-50" y="0" width="130" height="600"/>
                </clipPath>
            </defs>
            <g clip-path="url(#waveClip)">
                <!-- Layer 1 (Back - Light, widest swell, slowest) -->
                <path class="sso-wave-layer-bg" fill="rgba(165, 180, 252, 0.45)" stroke="none">
                    <animate
                        attributeName="d"
                        dur="9s"
                        repeatCount="indefinite"
                        calcMode="spline"
                        keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1"
                        keyTimes="0; 0.25; 0.5; 0.75; 1"
                        values="
                            M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z;
                            M80,0 L10,0 Q55,150 10,300 Q-30,450 80,600 Z;
                            M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z;
                            M80,0 L10,0 Q55,150 10,300 Q-30,450 80,600 Z;
                            M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z"
                    />
                </path>

                <!-- Layer 2 (Mid - Medium blue, offset phase -2s) -->
                <path class="sso-wave-layer-mid" fill="rgba(59, 130, 246, 0.7)" stroke="none">
                    <animate
                        attributeName="d"
                        dur="9s"
                        begin="-3s"
                        repeatCount="indefinite"
                        calcMode="spline"
                        keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1"
                        keyTimes="0; 0.25; 0.5; 0.75; 1"
                        values="
                            M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z;
                            M80,0 L20,0 Q62,150 20,300 Q-20,450 80,600 Z;
                            M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z;
                            M80,0 L20,0 Q62,150 20,300 Q-20,450 80,600 Z;
                            M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z"
                    />
                </path>

                <!-- Layer 3 (Front - Solid royal blue gradient matching panel) -->
                <path class="sso-wave-layer-front" stroke="none">
                    <animate
                        attributeName="d"
                        dur="9s"
                        begin="-6s"
                        repeatCount="indefinite"
                        calcMode="spline"
                        keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1"
                        keyTimes="0; 0.25; 0.5; 0.75; 1"
                        values="
                            M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z;
                            M80,0 L32,0 Q68,150 32,300 Q-8,450 80,600 Z;
                            M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z;
                            M80,0 L32,0 Q68,150 32,300 Q-8,450 80,600 Z;
                            M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z"
                    />
                </path>
            </g>
        </svg>

        <!-- Subtle Background Icons / watermark pattern -->
        <div class="sso-pattern-bg" aria-hidden="true">
            <svg class="pat-icon p1" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <svg class="pat-icon p2" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
            <svg class="pat-icon p3" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            <svg class="pat-icon p4" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <svg class="pat-icon p5" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <svg class="pat-icon p6" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
        </div>

        <div class="sso-form-wrap">
            <div class="sso-form-header">
                <h2 class="sso-title">LOGIN JURNAL</h2>
                <p class="sso-subtitle">SISTEM KBM & PRESENSI</p>
            </div>

            @if(session('success'))
                <div class="sso-alert sso-alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="sso-alert sso-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="sso-alert sso-alert-info">
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any() && !session('error'))
                <div class="sso-alert sso-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST" class="sso-form">
                @csrf

                <!-- Username Pill Input -->
                <div class="sso-input-pill">
                    <span class="sso-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" class="sso-input" placeholder="Username / NIP / NISN" required autofocus autocomplete="username">
                </div>

                <!-- Password Pill Input -->
                <div class="sso-input-pill">
                    <span class="sso-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" class="sso-input" placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" class="sso-eye-btn" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan atau sembunyikan password" tabindex="-1" style="background: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; padding: 6px 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; -webkit-tap-highlight-color: transparent;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>

                <!-- Pill Submit Button -->
                <button type="submit" class="sso-submit-btn">
                    Masuk ke Sistem
                </button>

                <div style="text-align: center; margin-top: 16px;">
                    <a href="{{ route('lupa-password') }}" class="lupa-link-btn" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 20px; color: var(--accent, #2563eb); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.25s ease;" onmouseover="this.style.background='rgba(59,130,246,0.16)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='rgba(59,130,246,0.08)'; this.style.transform='none';">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Lupa Password atau Username?</span>
                    </a>
                </div>
            </form>

            <div class="sso-right-footer">
                <span>Hak Cipta &copy; {{ date('Y') }} Jurnal Sekolah. Seluruh hak cipta dilindungi.</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const canvas = document.getElementById('starCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const ssoContainer = document.getElementById('ssoContainer');

    let W = 0, H = 0, stars = [];

    function resize() {
        const parent = canvas.parentElement;
        if (!parent || parent.offsetWidth === 0) return;
        W = canvas.width  = parent.offsetWidth;
        H = canvas.height = parent.offsetHeight;
        initStars();
    }
    window.resizeStarCanvas = resize;

    function initStars() {
        stars = [];
        const count = Math.floor((W * H) / 3500);
        for (let i = 0; i < count; i++) {
            stars.push({
                x: Math.random() * W,
                y: Math.random() * H,
                r: Math.random() * 1.4 + 0.3,
                alpha: Math.random(),
                dAlpha: (Math.random() * 0.008 + 0.002) * (Math.random() < 0.5 ? 1 : -1),
                color: ['rgba(255,255,255,','rgba(200,220,255,','rgba(255,240,200,','rgba(180,210,255,'][Math.floor(Math.random()*4)]
            });
        }
    }

    function draw() {
        // Hanya render saat panel terbuka agar transisi mulus dan hemat beban komputasi
        if (!ssoContainer || !ssoContainer.classList.contains('sso-minimized')) {
            if (W > 0 && H > 0) {
                ctx.clearRect(0, 0, W, H);
                for (let i = 0; i < stars.length; i++) {
                    const s = stars[i];
                    s.alpha += s.dAlpha;
                    if (s.alpha >= 1)      { s.alpha = 1;   s.dAlpha = -Math.abs(s.dAlpha); }
                    else if (s.alpha <= 0) { s.alpha = 0;   s.dAlpha =  Math.abs(s.dAlpha); }
                    ctx.beginPath();
                    ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                    ctx.fillStyle = s.color + s.alpha + ')';
                    ctx.fill();
                }
            }
        }
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    resize();
    draw();
})();

/* ── Handler Toggle Minimize & Restore Form Login ── */
(function() {
    const ssoContainer = document.getElementById('ssoContainer');
    const minimizeBtn = document.getElementById('toggleMinimizeBtn');
    const restoreBtn = document.getElementById('toggleRestoreBtn');

    function toggleForm() {
        if (!ssoContainer) return;
        const isMinimized = ssoContainer.classList.toggle('sso-minimized');

        if (minimizeBtn) {
            const label = isMinimized ? 'Buka Form Login' : 'Tutup Form Login';
            minimizeBtn.setAttribute('title', label);
            minimizeBtn.setAttribute('aria-label', label);
        }

        // Jalankan penyesuaian canvas khusus setelah animasi transisi selesai tanpa membebani browser
        if (!isMinimized) {
            setTimeout(function() {
                if (typeof window.resizeStarCanvas === 'function') {
                    window.resizeStarCanvas();
                }
            }, 390);
        }
    }

    // Jika kembali dari halaman lupa password / reset password, langsung tampilkan form login
    if (document.referrer && (document.referrer.includes('lupa-password') || document.referrer.includes('reset-password'))) {
        if (ssoContainer && ssoContainer.classList.contains('sso-minimized')) {
            ssoContainer.classList.remove('sso-minimized');
            if (typeof window.resizeStarCanvas === 'function') {
                setTimeout(window.resizeStarCanvas, 50);
            }
        }
    }

    if (minimizeBtn) {
        const isMin = ssoContainer && ssoContainer.classList.contains('sso-minimized');
        minimizeBtn.setAttribute('title', isMin ? 'Buka Form Login' : 'Tutup Form Login');
        minimizeBtn.setAttribute('aria-label', isMin ? 'Buka Form Login' : 'Tutup Form Login');

        minimizeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleForm();
        });
    }

    if (restoreBtn) {
        restoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleForm();
        });
    }

    window.toggleSSOLogin = toggleForm;
})();
</script>
@endpush
