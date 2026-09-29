@extends('layouts.guest')

@section('title', 'Login — Jurnal Sekolah')

@section('content')
<style>
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

/* Theme Toggle Button (Corner) */
.sso-theme-btn-corner {
    position: fixed !important;
    top: 20px !important;
    right: 24px !important;
    z-index: 999 !important;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #93c5fd;
    backdrop-filter: blur(10px);
    transition: all 0.25s ease;
}
.sso-theme-btn-corner:hover {
    background: rgba(15, 23, 42, 0.85);
    color: #ffffff;
    transform: scale(1.05);
}
[data-theme="light"] .sso-theme-btn-corner {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(30, 58, 138, 0.2);
    color: #1e3a8a;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
[data-theme="light"] .sso-theme-btn-corner .sun-icon {
    display: none;
}
[data-theme="light"] .sso-theme-btn-corner .moon-icon {
    display: block;
}
[data-theme="dark"] .sso-theme-btn-corner .sun-icon {
    display: block;
}
[data-theme="dark"] .sso-theme-btn-corner .moon-icon {
    display: none;
}

/* ===== TRANSISI HALUS SSO (EXPAND / COLLAPSE) ===== */
.sso-container {
    transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                max-width 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                min-height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                border-radius 0.38s cubic-bezier(0.16, 1, 0.3, 1) !important;
    will-change: width, height, max-width;
    position: relative;
}

.sso-left {
    border-radius: 24px 0 0 24px;
    overflow: hidden;
    transition: padding 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                border-radius 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.sso-right {
    border-radius: 0 24px 24px 0;
    overflow: visible;
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
    top: 14px !important;
    right: 16px !important;
    z-index: 50;
    width: 32px !important;
    height: 32px !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: rgba(255, 255, 255, 0.85);
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 8px !important;
    outline: none !important;
    transition: all 0.2s ease;
}

.sso-close-btn:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff;
    transform: scale(1.05);
}

[data-theme="light"] .sso-close-btn {
    background: rgba(30, 58, 138, 0.08) !important;
    border: 1px solid rgba(30, 58, 138, 0.18) !important;
    color: #1e3a8a;
}

[data-theme="light"] .sso-close-btn:hover {
    background: rgba(30, 58, 138, 0.16) !important;
    color: #1d4ed8;
}

/* Sembunyikan tombol minimize saat minimized */
.sso-container.sso-minimized .sso-close-btn {
    display: none !important;
}

/* Tombol Buka Form Login saat Minimized */
.sso-restore-action-box {
    display: none;
    margin-top: 24px;
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

/* SAAT MINIMIZE AKTIF — KOTAK JADI COMPACT SQUARE */
.sso-container.sso-minimized {
    width: min(420px, 86vh, 92vw) !important;
    height: auto !important;
    max-width: min(420px, 86vh, 92vw) !important;
    min-height: 380px !important;
    margin: auto !important;
    background-color: #0b1329 !important;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
    border-radius: 28px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
}

[data-theme="light"] .sso-container.sso-minimized {
    background-color: #ffffff !important;
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(191, 219, 254, 0.9) !important;
}

/* Sembunyikan panel kanan saat minimized */
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
}

/* Panel kiri saat minimized */
.sso-container.sso-minimized .sso-left {
    flex: 1 1 100% !important;
    width: 100% !important;
    height: 100% !important;
    align-items: center !important;
    text-align: center !important;
    padding: 36px 28px !important;
    justify-content: center !important;
    gap: 16px !important;
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
    gap: 10px !important;
    padding: 0 !important;
}

.sso-container.sso-minimized .sso-welcome-title {
    font-size: 26px !important;
    font-weight: 800 !important;
    margin-bottom: 2px !important;
    color: #38bdf8 !important;
}

[data-theme="light"] .sso-container.sso-minimized .sso-welcome-title {
    color: #1d4ed8 !important;
}

.sso-container.sso-minimized .sso-welcome-desc {
    margin: 0 auto !important;
    text-align: center !important;
    max-width: 320px !important;
    font-size: 13.5px !important;
    line-height: 1.6 !important;
    color: #cbd5e1 !important;
    opacity: 0.95 !important;
}

[data-theme="light"] .sso-container.sso-minimized .sso-welcome-desc {
    color: #334155 !important;
}

.sso-container.sso-minimized .sso-left-footer {
    display: none !important;
}
</style>

<!-- Theme Toggle Button -->
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
    <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>
</button>

<div class="sso-container {{ ($errors->any() || session('error') || session('success') || session('info') || old('username') || request()->has('form') || request('open')) ? '' : 'sso-minimized' }}" id="ssoContainer">

    <!-- LEFT SIDE: Branding & Welcome -->
    <div class="sso-left">
        <div class="sso-brand">
            <div class="sso-brand-icon" style="background: #ffffff; padding: 4px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.25); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
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
            <svg width="16" height="3" viewBox="0 0 16 3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="1" y1="1.5" x2="15" y2="1.5"></line>
            </svg>
        </button>

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
                <h2 class="sso-title">LOGIN PORTAL</h2>
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
                    <button type="button" class="sso-eye-btn" onclick="togglePasswordVisibility('password', this)" title="Tampilkan/Sembunyikan Password" tabindex="-1">
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

<script>
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
    }

    if (minimizeBtn) {
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

/* Reset password status check */
document.addEventListener('DOMContentLoaded', function() {
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    const token = getCookie('reset_token') || localStorage.getItem('reset_token');

    if (token) {
        fetch(`{{ route('lupa-password.check-status') }}?token=${encodeURIComponent(token)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'approved' && data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            })
            .catch(err => console.error('Status check error:', err));
    }
});
</script>
@endsection
