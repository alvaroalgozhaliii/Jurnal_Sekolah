@extends('layouts.guest')

@section('title', 'Reset Password Baru — Jurnal Sekolah')

@section('content')
<div class="sso-container">

    <!-- LEFT SIDE: Branding & Welcome -->
    <div class="sso-left">
        <div class="sso-brand">
            <div class="sso-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                </svg>
            </div>
            <div class="sso-brand-text">
                <span class="brand-name">Jurnal Sekolah</span>
                <span class="brand-tag">RESET SANDI</span>
            </div>
        </div>

        <div class="sso-welcome-box">
            <h1 class="sso-welcome-title">SETEL KATA SANDI BARU</h1>
            <p class="sso-welcome-desc">Pengajuan Anda telah disetujui oleh Admin. Silakan buat password baru untuk melanjutkan</p>
        </div>

        <div class="sso-left-footer">
            <span>&copy; {{ date('Y') }} Jurnal Sekolah. All rights reserved.</span>
        </div>
    </div>

    <!-- RIGHT SIDE: Curved Wave Form Portal -->
    <div class="sso-right">
        <!-- Theme Toggle Button -->
        <button type="button" id="themeToggleBtn" class="theme-toggle-btn sso-theme-btn-corner" aria-label="Toggle Mode Gelap/Terang" title="Ganti Mode Gelap / Terang">
            <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="1" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
            <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px; height:18px;">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>

        <!-- Animated 3-Layered Wave Divider -->
        <svg class="sso-wave-svg" viewBox="0 0 80 600" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <clipPath id="waveClip">
                    <rect x="-50" y="0" width="130" height="600"/>
                </clipPath>
            </defs>
            <g clip-path="url(#waveClip)">
                <path class="sso-wave-layer-bg" fill="rgba(165, 180, 252, 0.45)" stroke="none">
                    <animate attributeName="d" dur="9s" repeatCount="indefinite" calcMode="spline" keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1" keyTimes="0; 0.25; 0.5; 0.75; 1" values="M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z; M80,0 L10,0 Q55,150 10,300 Q-30,450 80,600 Z; M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z; M80,0 L10,0 Q55,150 10,300 Q-30,450 80,600 Z; M80,0 L10,0 Q-30,150 10,300 Q55,450 80,600 Z"/>
                </path>
                <path class="sso-wave-layer-mid" fill="rgba(59, 130, 246, 0.7)" stroke="none">
                    <animate attributeName="d" dur="9s" begin="-3s" repeatCount="indefinite" calcMode="spline" keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1" keyTimes="0; 0.25; 0.5; 0.75; 1" values="M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z; M80,0 L20,0 Q62,150 20,300 Q-20,450 80,600 Z; M80,0 L10,0 Q-20,150 20,300 Q62,450 80,600 Z; M80,0 L10,0 Q62,150 20,300 Q-20,450 80,600 Z; M80,0 L10,0 Q-20,150 20,300 Q62,450 80,600 Z"/>
                </path>
                <path class="sso-wave-layer-front" fill="currentColor" stroke="none">
                    <animate attributeName="d" dur="9s" begin="-6s" repeatCount="indefinite" calcMode="spline" keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1" keyTimes="0; 0.25; 0.5; 0.75; 1" values="M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z; M80,0 L32,0 Q68,150 32,300 Q-8,450 80,600 Z; M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z; M80,0 L32,0 Q68,150 32,300 Q-8,450 80,600 Z; M80,0 L32,0 Q-8,150 32,300 Q68,450 80,600 Z"/>
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
                <h2 class="sso-title">NEW PASSWORD</h2>
                <p class="sso-subtitle">CREATE NEW PASSWORD FOR {{ strtoupper($user->nama) }}</p>
            </div>

            @if(session('error'))
                <div class="sso-alert sso-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="sso-alert sso-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </span>
                </div>
            @endif

            <!-- USERNAME INFORMATIONAL PILL -->
            <div style="background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.25); border-radius: 20px; padding: 10px 16px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 12.5px; color: var(--text-secondary, #64748b); font-weight: 600;">Username Anda:</span>
                <code style="font-size: 14px; font-weight: 800; color: #2563eb; letter-spacing: 0.5px; background: rgba(37, 99, 235, 0.12); padding: 2px 8px; border-radius: 6px;">{{ $user->username }}</code>
            </div>

            <form action="{{ route('reset-password.process', ['token' => $token]) }}" method="POST" class="sso-form">
                @csrf

                <!-- Password Baru Pill Input -->
                <div class="sso-input-pill">
                    <span class="sso-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" class="sso-input" placeholder="Masukkan Password Baru (Min 6 Karakter)" required autocomplete="new-password">
                    <button type="button" class="sso-eye-btn" onclick="togglePasswordVisibility('password', this)" title="Tampilkan/Sembunyikan Password" tabindex="-1">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>

                <!-- Konfirmasi Password Baru Pill Input -->
                <div class="sso-input-pill">
                    <span class="sso-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="sso-input" placeholder="Ketik Ulang Password Baru" required autocomplete="new-password">
                    <button type="button" class="sso-eye-btn" onclick="togglePasswordVisibility('password_confirmation', this)" title="Tampilkan/Sembunyikan Password" tabindex="-1">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>

                <!-- Green Pill Submit Button -->
                <button type="submit" class="sso-submit-btn">
                    Simpan Password Baru & Login
                </button>
            </form>

            <div class="sso-right-footer">
                <span>Copyright &copy; {{ date('Y') }} Jurnal Sekolah. All rights reserved.</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('reset_token');
});
</script>
@endsection
