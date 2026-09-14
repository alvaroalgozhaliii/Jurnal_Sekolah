@extends('layouts.guest')

@section('title', 'Pilih Akses — Jurnal Sekolah')

@section('content')
<div class="pilih-akses-container" style="width: 100%; max-width: 960px; margin: 0 auto; padding: 24px 16px; position: relative; z-index: 10;">
    
    <!-- Theme Toggle Corner Button -->
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

    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 32px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 9999px; color: #2563eb; font-size: 13px; font-weight: 600; margin-bottom: 12px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Selamat Datang, {{ $user->nama }}</span>
        </div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-primary, #0f172a); margin: 0 0 8px 0; letter-spacing: -0.5px;">
            Pilih Akses
        </h1>
        <p style="font-size: 15px; color: var(--text-secondary, #64748b); margin: 0 auto; max-width: 500px;">
            Silakan pilih akses yang ingin Anda gunakan. Anda dapat berganti peran kapan saja melalui menu profil.
        </p>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div style="margin-bottom: 24px; padding: 12px 16px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 12px; color: #991b1b; display: flex; align-items: center; gap: 10px; font-size: 14px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 32px;">
        @foreach($accesses as $key => $item)
            @php
                $cardColor = match($key) {
                    'wali_kelas' => '#059669',
                    'waka'       => '#d97706',
                    'kepala_sekolah' => '#7c3aed',
                    default      => '#2563eb',
                };
                $cardBg = match($key) {
                    'wali_kelas' => 'rgba(16, 185, 129, 0.12)',
                    'waka'       => 'rgba(245, 158, 11, 0.12)',
                    'kepala_sekolah' => 'rgba(124, 58, 237, 0.12)',
                    default      => 'rgba(59, 130, 246, 0.12)',
                };
            @endphp
            <div class="akses-card" style="background: var(--bg-card, #ffffff); border: 1.5px solid var(--border, #e2e8f0); border-radius: 20px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 8px 24px rgba(0,0,0,0.04); transition: all 0.25s ease; position: relative; overflow: hidden;">
                
                <!-- Card Header with Icon & Badge -->
                <div>
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: {{ $cardBg }}; color: {{ $cardColor }}; display: flex; align-items: center; justify-content: center;">
                            @if($key === 'guru')
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            @elseif($key === 'wali_kelas')
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            @elseif($key === 'kepala_sekolah')
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            @else
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            @endif
                        </div>

                        @if(!empty($item['badge']))
                            <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px; background: {{ $cardBg }}; color: {{ $cardColor }}; text-transform: uppercase;">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </div>

                    <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary, #0f172a); margin: 0 0 6px 0;">
                        {{ $item['title'] }}
                    </h3>
                    <div style="font-size: 13.5px; font-weight: 600; color: {{ $cardColor }}; margin-bottom: 8px;">
                        {{ $item['subtitle'] }}
                    </div>
                    <p style="font-size: 13px; color: var(--text-secondary, #64748b); line-height: 1.5; margin: 0 0 20px 0;">
                        {{ $item['description'] }}
                    </p>
                </div>

                <!-- Submit Action Button -->
                <form action="{{ route('pilih-akses.simpan') }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="akses" value="{{ $key }}">
                    <button type="submit" style="width: 100%; padding: 12px 18px; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease; background: {{ $cardColor }}; color: #ffffff;" onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)';" onmouseout="this.style.opacity='1'; this.style.transform='none';">
                        <span>Masuk sebagai {{ $item['title'] }}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    <!-- Footer Logout Button -->
    <div style="text-align: center;">
        <form action="{{ route('logout') }}" method="POST" style="display: inline-block;">
            @csrf
            <button type="submit" style="background: none; border: none; padding: 8px 16px; color: var(--text-secondary, #64748b); font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: underline; transition: color 0.2s ease;" onmouseover="this.style.color='#ef4444';" onmouseout="this.style.color='var(--text-secondary, #64748b)';">
                Bukan akun Anda? Keluar (Logout)
            </button>
        </form>
    </div>
</div>

<style>
.akses-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08) !important;
    border-color: #3b82f6 !important;
}
[data-theme="dark"] .akses-card {
    background: #1e293b !important;
    border-color: #334155 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
[data-theme="dark"] .akses-card:hover {
    border-color: #60a5fa !important;
}
</style>
@endsection
