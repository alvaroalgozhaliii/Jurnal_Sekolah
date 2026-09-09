@extends('layouts.guest')

@section('title', 'Lupa Password & Username — Jurnal Sekolah')

@section('content')
<div class="sso-container">

    <!-- LEFT SIDE: Branding & Welcome -->
    <div class="sso-left">
        <div class="sso-brand">
            <div class="sso-brand-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="sso-brand-text">
                <span class="brand-name">Jurnal Sekolah</span>
                <span class="brand-tag">LUPA AKUN</span>
            </div>
        </div>

        <div class="sso-welcome-box">
            <h1 class="sso-welcome-title">LUPA AKUN ?</h1>
            <p class="sso-welcome-desc">Masukkan NISN (untuk Orang Tua) atau NIK (untuk Guru/Staf) untuk mengajukan reset kata sandi ke Admin</p>
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
                <line x1="1" y1="12" x2="3" y2="12"></line>
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
                    <animate attributeName="d" dur="9s" begin="-3s" repeatCount="indefinite" calcMode="spline" keySplines="0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1; 0.45 0 0.55 1" keyTimes="0; 0.25; 0.5; 0.75; 1" values="M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z; M80,0 L20,0 Q62,150 20,300 Q-20,450 80,600 Z; M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z; M80,0 L20,0 Q62,150 20,300 Q-20,450 80,600 Z; M80,0 L20,0 Q-20,150 20,300 Q62,450 80,600 Z"/>
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
                <h2 class="sso-title">RESET ACCOUNT</h2>
                <p class="sso-subtitle">SUBMIT REQUEST TO ADMIN</p>
            </div>

            @if(session('error'))
                <div class="sso-alert sso-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="sso-alert sso-alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="sso-alert sso-alert-info">
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- PENDING STATUS CARD (Auto-Polled) -->
            <div id="pendingStatusCard" style="display: none; background: rgba(245, 158, 11, 0.1); border: 1.5px solid rgba(245, 158, 11, 0.4); border-radius: 20px; padding: 14px 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: #f59e0b; animation: pulse 1.5s infinite;"></span>
                    <strong style="font-size: 13.5px; color: var(--text-primary, #1e293b);">Menunggu Persetujuan Admin</strong>
                </div>
                <div id="pengajuDetailText" style="font-size: 12px; color: var(--text-secondary, #64748b);">Sedang memproses permohonan...</div>
            </div>

            <form action="{{ route('lupa-password.submit') }}" method="POST" class="sso-form" id="lupaForm">
                @csrf

                <!-- Role Segmented Selector (Sleek Pill Style) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: rgba(0,0,0,0.04); padding: 4px; border-radius: 24px; margin-bottom: 14px; border: 1px solid var(--border, #e2e8f0);">
                    <label id="tabOrtu" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 12px; border-radius: 20px; cursor: pointer; font-size: 12.5px; font-weight: 700; transition: all 0.25s;">
                        <input type="radio" name="role_tipe" value="ortu" {{ old('role_tipe', 'ortu') === 'ortu' ? 'checked' : '' }} onchange="toggleRoleInput('ortu')" style="display: none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        <span>Ortu / Siswa</span>
                    </label>

                    <label id="tabGuru" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 12px; border-radius: 20px; cursor: pointer; font-size: 12.5px; font-weight: 700; transition: all 0.25s;">
                        <input type="radio" name="role_tipe" value="guru_staf" {{ old('role_tipe') === 'guru_staf' ? 'checked' : '' }} onchange="toggleRoleInput('guru_staf')" style="display: none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        <span>Guru / Staf</span>
                    </label>
                </div>

                <!-- NISN / NIK Input Pill (Identical dimensions to login username pill) -->
                <div class="sso-input-pill">
                    <span class="sso-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" id="nisn_nik" name="nisn_nik" value="{{ old('nisn_nik') }}" class="sso-input" placeholder="Masukkan NISN Anak Anda" required autofocus autocomplete="off">
                </div>

                <!-- Green Pill Submit Button (Identical dimensions to login submit button) -->
                <button type="submit" class="sso-submit-btn">
                    Kirim Permohonan
                </button>

                <div style="text-align: center; margin-top: 14px;">
                    <a href="{{ route('login') }}" class="lupa-link-btn" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 20px; color: var(--accent, #2563eb); font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all 0.25s ease;" onmouseover="this.style.background='rgba(59,130,246,0.16)'" onmouseout="this.style.background='rgba(59,130,246,0.08)'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        <span>Kembali ke Login</span>
                    </a>
                </div>
            </form>

            <div class="sso-right-footer">
                <span>Copyright &copy; {{ date('Y') }} Jurnal Sekolah. All rights reserved.</span>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.4; }
    50% { opacity: 1; }
    100% { opacity: 0.4; }
}
</style>

<script>
function toggleRoleInput(role) {
    const input = document.getElementById('nisn_nik');
    const tabOrtu = document.getElementById('tabOrtu');
    const tabGuru = document.getElementById('tabGuru');

    if (role === 'ortu') {
        input.placeholder = 'Masukkan NISN Anak Anda';
        tabOrtu.style.background = '#2563eb';
        tabOrtu.style.color = '#ffffff';
        tabOrtu.style.boxShadow = '0 3px 10px rgba(37, 99, 235, 0.3)';

        tabGuru.style.background = 'transparent';
        tabGuru.style.color = 'var(--text-secondary, #64748b)';
        tabGuru.style.boxShadow = 'none';
    } else {
        input.placeholder = 'Masukkan NIK atau NIP Anda';
        tabGuru.style.background = '#2563eb';
        tabGuru.style.color = '#ffffff';
        tabGuru.style.boxShadow = '0 3px 10px rgba(37, 99, 235, 0.3)';

        tabOrtu.style.background = 'transparent';
        tabOrtu.style.color = 'var(--text-secondary, #64748b)';
        tabOrtu.style.boxShadow = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const initialRole = document.querySelector('input[name="role_tipe"]:checked')?.value || 'ortu';
    toggleRoleInput(initialRole);

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    const currentToken = "{{ $token ?? '' }}" || getCookie('reset_token') || localStorage.getItem('reset_token');

    if (currentToken) {
        localStorage.setItem('reset_token', currentToken);

        function checkStatus() {
            fetch(`{{ route('lupa-password.check-status') }}?token=${encodeURIComponent(currentToken)}`)
                .then(res => res.json())
                .then(data => {
                    const statusCard = document.getElementById('pendingStatusCard');
                    const pengajuText = document.getElementById('pengajuDetailText');

                    if (data.status === 'pending') {
                        if (statusCard) statusCard.style.display = 'block';
                        if (pengajuText && data.nama_pengaju) {
                            pengajuText.innerText = data.nama_pengaju + ' (' + data.updated_at + ')';
                        }
                    } else if (data.status === 'approved' && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else if (data.status === 'completed' || data.status === 'rejected' || data.status === 'not_found') {
                        if (statusCard) statusCard.style.display = 'none';
                        localStorage.removeItem('reset_token');
                    }
                })
                .catch(err => console.error('Status check error:', err));
        }

        checkStatus();
        setInterval(checkStatus, 5000);
    }
});
</script>
@endsection
