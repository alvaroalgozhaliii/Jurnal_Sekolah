@extends('layouts.guest')

@section('title', 'Login Guru — Jurnal Sekolah')

@section('content')
<div class="login-card">
    <!-- Theme Toggle Button -->
    <button type="button" id="themeToggleBtn" class="theme-toggle-btn sso-theme-btn-corner" aria-label="Toggle Mode Gelap/Terang" title="Ganti Mode Gelap / Terang">
        <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px; height:20px;">
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
        <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px; height:20px;">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>
    <div class="login-header">
        <div class="login-logo" style="background: #ffffff; padding: 6px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Jurnal Sekolah" style="width: 100%; height: 100%; object-fit: contain; display: block;">
        </div>
        <div class="login-title">LOGIN GURU</div>
        <div class="login-subtitle">Jurnal Sekolah & Presensi KBM</div>
        <div class="login-accent-line"></div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-16">
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username Guru</label>
            <div class="input-icon-wrapper">
                <span class="input-icon-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username guru" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-icon-wrapper">
                <span class="input-icon-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input type="password" id="password" name="password" class="form-control has-toggle" placeholder="Password guru" required>
                <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)" title="Tampilkan/Sembunyikan Password">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-16">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Masuk Portal Guru
        </button>

        <div class="login-divider">
            <span>atau</span>
        </div>

        <div class="login-footer-info">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M9 12l2 2 4-4"></path>
            </svg>
            <span>Akses khusus Bapak/Ibu Guru</span>
        </div>
    </form>
</div>
@endsection