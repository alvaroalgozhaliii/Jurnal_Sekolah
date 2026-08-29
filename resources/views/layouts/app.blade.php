<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jurnal Sekolah — Sistem Manajemen Information KBM')</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Early Theme Detection (Prevents FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('jurnal_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/jurnal.css') }}">

    <!-- Chart.js Engine CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tom Select (Searchable Dropdown System) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
    /* Custom Searchable Select Styling - Responsive Flow (Pushes Content Down Inside Card) */
    .ts-wrapper.form-control, .ts-wrapper.select-search {
        padding: 0;
        border: none;
        background: transparent;
        width: 100%;
        display: block;
    }
    .ts-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 9px 12px !important;
        font-size: 14px !important;
        font-family: 'Inter', -apple-system, sans-serif !important;
        min-height: 40px !important;
        box-shadow: none !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
        width: 100% !important;
        cursor: pointer;
    }
    .ts-control:focus, .ts-wrapper.focus .ts-control {
        border-color: #1e3a8a !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15) !important;
        background: #ffffff !important;
    }
    .ts-control input {
        font-family: 'Inter', -apple-system, sans-serif !important;
        font-size: 14px !important;
        color: #1e293b !important;
    }
    .ts-dropdown {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: 100% !important;
        margin-top: 6px !important;
        margin-bottom: 8px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04) !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        opacity: 1 !important;
        overflow: hidden !important;
        z-index: 10 !important;
    }
    .ts-dropdown-content {
        max-height: 220px !important;
        overflow-y: auto !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
    }
    .ts-dropdown .option {
        padding: 10px 14px !important;
        color: #1e293b !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        font-size: 13.5px !important;
    }
    .ts-dropdown .option:last-child {
        border-bottom: none;
    }
    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #eff6ff !important;
        color: #1e3a8a !important;
        font-weight: 600 !important;
    }
    .ts-dropdown .option.selected {
        background-color: #1e3a8a !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .ts-dropdown .highlight {
        background: #fef08a !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        border-radius: 2px;
        padding: 0 2px;
    }
    .ts-dropdown .no-results {
        padding: 12px 14px !important;
        color: #64748b !important;
        font-style: italic;
        background: #ffffff !important;
        background-color: #ffffff !important;
    }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile backdrop) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-shell">

    <!-- SIDEBAR NAVY -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </div>
            <div>
                <div class="sidebar-brand-title">JURNAL SEKOLAH</div>
                <div class="sidebar-brand-subtitle">Sistem Presensi KBM</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @auth
            @php $role = Auth::user()->role; @endphp

            <!-- ADMIN -->
            @if($role === 'admin')
                <div class="nav-section-label">Dashboard & Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Admin
                </a>
                <a href="{{ route('admin.rekap-kehadiran') }}" class="nav-item {{ request()->routeIs('admin.rekap-kehadiran') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Rekap Kehadiran
                </a>

                <div class="nav-section-label">Master Data</div>
                <a href="{{ route('guru.index') }}" class="nav-item {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Data Guru
                </a>
                <a href="{{ route('siswa.index') }}" class="nav-item {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    Data Siswa
                </a>
                <a href="{{ route('kelas.index') }}" class="nav-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    Data Kelas
                </a>
                <a href="{{ route('jurusan.index') }}" class="nav-item {{ request()->routeIs('jurusan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    Data Jurusan
                </a>
                <a href="{{ route('mapel.index') }}" class="nav-item {{ request()->routeIs('mapel.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    Mata Pelajaran
                </a>
                <a href="{{ route('jadwal.index') }}" class="nav-item {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Jadwal Pelajaran
                </a>
                <a href="{{ route('pengguna.index') }}" class="nav-item {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Manajemen Akun User
                </a>

                <div class="nav-section-label">Pengawasan & Pengaturan</div>
                <a href="{{ route('jurnal-harian.index') }}" class="nav-item {{ request()->routeIs('jurnal-harian.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Jurnal Harian KBM
                </a>
                <a href="{{ route('pengajuan.index') }}" class="nav-item {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Izin
                </a>
                <a href="{{ route('admin.backup') }}" class="nav-item {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Backup Database
                </a>

            <!-- GURU -->
            @elseif($role === 'guru')
                <div class="nav-section-label">Dashboard & Presensi</div>
                <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Guru
                </a>
                <a href="{{ route('guru.presensi-saya') }}" class="nav-item {{ request()->routeIs('guru.presensi-saya') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Presensi Masuk / Keluar
                </a>

                <div class="nav-section-label">Aktivitas Mengajar KBM</div>
                <a href="{{ route('jurnal-harian.index') }}" class="nav-item {{ request()->routeIs('jurnal-harian.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Jurnal Mengajar Saya
                </a>
                <a href="{{ route('absensi-siswa.index') }}" class="nav-item {{ request()->routeIs('absensi-siswa.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                    Absensi Siswa KBM
                </a>
                <a href="{{ route('pengajuan.index') }}" class="nav-item {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Pengajuan Izin Guru
                </a>

            <!-- PIKET -->
            @elseif($role === 'piket')
                <div class="nav-section-label">Area Petugas Piket</div>
                <a href="{{ route('piket.dashboard') }}" class="nav-item {{ request()->routeIs('piket.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Piket
                </a>
                <a href="{{ route('piket.presensi') }}" class="nav-item {{ request()->routeIs('piket.presensi') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    Presensi Guru Piket
                </a>
                <a href="{{ route('piket.anak-sakit') }}" class="nav-item {{ request()->routeIs('piket.anak-sakit') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18"></path><path d="M6 6l12 12"></path></svg>
                    Pencatatan Siswa Sakit
                </a>
                <a href="{{ route('pengajuan.index') }}" class="nav-item {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Verifikasi Izin Siswa
                </a>

            <!-- ORTU / SISWA -->
            @elseif($role === 'ortu' || $role === 'siswa')
                <div class="nav-section-label">Portal Orang Tua</div>
                <a href="{{ route('ortu.dashboard') }}" class="nav-item {{ request()->routeIs('ortu.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Ortu
                </a>
                <a href="{{ route('ortu.data-anak') }}" class="nav-item {{ request()->routeIs('ortu.data-anak') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profil Data Anak
                </a>
                <a href="{{ route('ortu.presensi') }}" class="nav-item {{ request()->routeIs('ortu.presensi') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    Presensi Kehadiran Anak
                </a>
                <a href="{{ route('ortu.rekap-bulanan') }}" class="nav-item {{ request()->routeIs('ortu.rekap-bulanan') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Rekap Presensi Bulanan
                </a>
                <a href="{{ route('ortu.jadwal-anak') }}" class="nav-item {{ request()->routeIs('ortu.jadwal-anak') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                    Jadwal Pelajaran Anak
                </a>
                <a href="{{ route('pengajuan.index') }}" class="nav-item {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Pengajuan Izin Anak
                </a>

            <!-- WALI KELAS -->
            @elseif($role === 'walikelas')
                <div class="nav-section-label">Area Wali Kelas</div>
                <a href="{{ route('walikelas.dashboard') }}" class="nav-item {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Wali Kelas
                </a>
                <a href="{{ route('walikelas.data-kelas') }}" class="nav-item {{ request()->routeIs('walikelas.data-kelas') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    Data Siswa Kelas
                </a>
                <a href="{{ route('walikelas.jurnal') }}" class="nav-item {{ request()->routeIs('walikelas.jurnal') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Jurnal KBM Kelas
                </a>
                <a href="{{ route('walikelas.rekap-presensi') }}" class="nav-item {{ request()->routeIs('walikelas.rekap-presensi') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Rekap Presensi Kelas
                </a>

            <!-- WAKA SDM -->
            @elseif($role === 'waka_sdm')
                <div class="nav-section-label">Waka SDM / Ketenagaan</div>
                <a href="{{ route('waka.dashboard') }}" class="nav-item {{ request()->routeIs('waka.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard SDM
                </a>
                <a href="{{ route('waka.persetujuan.index') }}" class="nav-item {{ request()->routeIs('waka.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- WAKA KESISWAAN -->
            @elseif($role === 'waka_kesiswaan')
                <div class="nav-section-label">Waka Kesiswaan</div>
                <a href="{{ route('waka.monitoring-siswa') }}" class="nav-item {{ request()->routeIs('waka.monitoring-siswa') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"></circle><path d="M3 21v-2a6 6 0 0 1 12 0v2"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path><path d="M21 21v-2a6 6 0 0 0-3-5.19"></path></svg>
                    Monitoring Siswa
                </a>
                <a href="{{ route('waka.persetujuan.index') }}" class="nav-item {{ request()->routeIs('waka.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- WAKA KURIKULUM -->
            @elseif($role === 'waka_kurikulum')
                <div class="nav-section-label">Waka Kurikulum</div>
                <a href="{{ route('waka-kurikulum.dashboard') }}" class="nav-item {{ request()->routeIs('waka-kurikulum.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Kurikulum
                </a>
                <a href="{{ route('waka-kurikulum.index') }}" class="nav-item {{ request()->routeIs('waka-kurikulum.index', 'waka-kurikulum.jadwal.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Jadwal Piket & Waka
                </a>
                <a href="{{ route('waka.persetujuan.index') }}" class="nav-item {{ request()->routeIs('waka.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- WAKA SARPRAS -->
            @elseif($role === 'waka_sarpras')
                <div class="nav-section-label">Waka Sarpras</div>
                <a href="{{ route('waka.sarpras') }}" class="nav-item {{ request()->routeIs('waka.sarpras') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Monitoring Sarpras & Ruang
                </a>
                <a href="{{ route('waka.persetujuan.index') }}" class="nav-item {{ request()->routeIs('waka.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- WAKA HUMAS -->
            @elseif($role === 'waka_humas')
                <div class="nav-section-label">Waka Humas / Hubin</div>
                <a href="{{ route('waka.humas') }}" class="nav-item {{ request()->routeIs('waka.humas') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Dinas Luar & Kemitraan
                </a>
                <a href="{{ route('waka.persetujuan.index') }}" class="nav-item {{ request()->routeIs('waka.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- KEPALA SEKOLAH -->
            @elseif($role === 'kepala_sekolah')
                <div class="nav-section-label">Kepala Sekolah</div>
                <a href="{{ route('kepala.dashboard') }}" class="nav-item {{ request()->routeIs('kepala.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard Utama
                </a>
                <a href="{{ route('kepala.persetujuan.index') }}" class="nav-item {{ request()->routeIs('kepala.persetujuan.*') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Persetujuan Dispen
                </a>

            <!-- SATPAM -->
            @elseif($role === 'satpam')
                <div class="nav-section-label">Petugas Keamanan</div>
                <a href="{{ route('satpam.dashboard') }}" class="nav-item {{ request()->routeIs('satpam.dashboard') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Dashboard Satpam
                </a>
            @endif

            <div class="nav-section-label">Akun & Pengaturan</div>
            <a href="{{ route('profil.show') }}" class="nav-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Profil Akun
            </a>
            <a href="{{ route('pengaturan.index') }}" class="nav-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Pengaturan
            </a>

            <form action="{{ route('logout') }}" method="POST" style="margin-top: 16px;">
                @csrf
                <button type="submit" class="nav-item" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer; color: #f87171;">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Keluar Sistem
                </button>
            </form>
            @endauth
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="main-area">
        <!-- TOPBAR -->
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <!-- Hamburger (shown on mobile only) -->
                <button class="topbar-hamburger" id="sidebarToggle" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="topbar-title">
                    @yield('page-title', 'Dashboard')
                </div>
            </div>

            <div class="topbar-right">
                @auth
                @php
                    $unreadCount = \App\Models\Notifikasi::where('id_user', Auth::id())->where('dibaca', false)->count();
                @endphp
                <a href="{{ route('notifikasi.index') }}" style="position: relative; display: flex; align-items: center; color: var(--text-secondary);">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 22px; height: 22px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    @if($unreadCount > 0)
                        <span class="badge badge-amber" style="position: absolute; top: -6px; right: -8px; padding: 2px 6px; font-size: 10px;">{{ $unreadCount }}</span>
                    @endif
                </a>

                <!-- Dark Mode Toggle Button -->
                <button type="button" id="themeToggleBtn" class="theme-toggle-btn" aria-label="Toggle Dark Mode" title="Ganti Mode Gelap / Terang">
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

                <div class="user-pill">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->nama }}</div>
                        <div class="user-role">{{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}</div>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- FLASH NOTIFICATION MESSAGES -->
        <main class="content-area">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div>
                        <ul style="margin-left: 16px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- DIGITAL CALENDAR ENGINE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(input => {
        if (input.dataset.calendarInit) return;
        input.dataset.calendarInit = 'true';

        const wrapper = document.createElement('div');
        wrapper.className = 'jurnal-datepicker-wrapper';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const visibleInput = document.createElement('input');
        visibleInput.type = 'text';
        visibleInput.className = (input.className || 'form-control') + ' jurnal-datepicker-input';
        visibleInput.readOnly = true;
        visibleInput.placeholder = 'Pilih Tanggal';
        wrapper.appendChild(visibleInput);

        input.type = 'hidden';

        const popover = document.createElement('div');
        popover.className = 'jurnal-calendar-popover';
        popover.innerHTML = `
            <div class="jurnal-cal-header">
                <button type="button" class="jurnal-cal-nav-btn btn-prev">&larr;</button>
                <div class="jurnal-cal-month-title">Bulan Tahun</div>
                <button type="button" class="jurnal-cal-nav-btn btn-next">&rarr;</button>
            </div>
            <div class="jurnal-cal-grid">
                <div class="jurnal-cal-day-label">Min</div>
                <div class="jurnal-cal-day-label">Sen</div>
                <div class="jurnal-cal-day-label">Sel</div>
                <div class="jurnal-cal-day-label">Rab</div>
                <div class="jurnal-cal-day-label">Kam</div>
                <div class="jurnal-cal-day-label">Jum</div>
                <div class="jurnal-cal-day-label">Sab</div>
            </div>
            <div class="jurnal-cal-grid dates-container"></div>
            <div class="jurnal-cal-footer">
                <button type="button" class="jurnal-cal-btn-today">Hari Ini</button>
                <button type="button" class="jurnal-cal-btn-clear">Bersihkan</button>
            </div>
        `;
        wrapper.appendChild(popover);

        let currDate = input.value ? new Date(input.value) : new Date();
        if (isNaN(currDate.getTime())) currDate = new Date();
        let viewMonth = currDate.getMonth();
        let viewYear = currDate.getFullYear();

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        function updateVisibleText() {
            if (input.value) {
                const parts = input.value.split('-');
                if (parts.length === 3) {
                    const d = parseInt(parts[2], 10);
                    const m = parseInt(parts[1], 10) - 1;
                    const y = parts[0];
                    visibleInput.value = `${d} ${monthNames[m]} ${y}`;
                }
            } else {
                visibleInput.value = '';
            }
        }
        updateVisibleText();

        function renderCalendar() {
            const monthTitle = popover.querySelector('.jurnal-cal-month-title');
            monthTitle.textContent = `${monthNames[viewMonth]} ${viewYear}`;

            const datesContainer = popover.querySelector('.dates-container');
            datesContainer.innerHTML = '';

            const firstDay = new Date(viewYear, viewMonth, 1).getDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            const prevMonthDays = new Date(viewYear, viewMonth, 0).getDate();

            for (let i = firstDay - 1; i >= 0; i--) {
                const cell = document.createElement('div');
                cell.className = 'jurnal-cal-date-cell other-month';
                cell.textContent = prevMonthDays - i;
                datesContainer.appendChild(cell);
            }

            const today = new Date();
            const selectedVal = input.value;

            for (let d = 1; d <= daysInMonth; d++) {
                const cell = document.createElement('div');
                cell.className = 'jurnal-cal-date-cell';
                cell.textContent = d;

                const dateStr = `${viewYear}-${String(viewMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                
                if (dateStr === selectedVal) cell.classList.add('selected');
                if (d === today.getDate() && viewMonth === today.getMonth() && viewYear === today.getFullYear()) {
                    cell.classList.add('today');
                }

                cell.addEventListener('click', function () {
                    input.value = dateStr;
                    updateVisibleText();
                    popover.classList.remove('active');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                datesContainer.appendChild(cell);
            }

            const totalCells = firstDay + daysInMonth;
            const remainingCells = 42 - totalCells;
            for (let i = 1; i <= remainingCells && totalCells + i <= 35; i++) {
                const cell = document.createElement('div');
                cell.className = 'jurnal-cal-date-cell other-month';
                cell.textContent = i;
                datesContainer.appendChild(cell);
            }
        }

        visibleInput.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.jurnal-calendar-popover.active').forEach(p => {
                if (p !== popover) p.classList.remove('active');
            });
            renderCalendar();
            popover.classList.toggle('active');
        });

        popover.querySelector('.btn-prev').addEventListener('click', function (e) {
            e.stopPropagation();
            viewMonth--;
            if (viewMonth < 0) { viewMonth = 11; viewYear--; }
            renderCalendar();
        });

        popover.querySelector('.btn-next').addEventListener('click', function (e) {
            e.stopPropagation();
            viewMonth++;
            if (viewMonth > 11) { viewMonth = 0; viewYear++; }
            renderCalendar();
        });

        popover.querySelector('.jurnal-cal-btn-today').addEventListener('click', function (e) {
            e.stopPropagation();
            const now = new Date();
            const dateStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            input.value = dateStr;
            updateVisibleText();
            popover.classList.remove('active');
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        popover.querySelector('.jurnal-cal-btn-clear').addEventListener('click', function (e) {
            e.stopPropagation();
            input.value = '';
            updateVisibleText();
            popover.classList.remove('active');
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.jurnal-datepicker-wrapper')) {
            document.querySelectorAll('.jurnal-calendar-popover.active').forEach(p => p.classList.remove('active'));
        }
    });
});
</script>

<!-- RESPONSIVE SIDEBAR TOGGLE (Mobile) -->
<script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggle) {
        toggle.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close on nav-item click (mobile UX)
    document.querySelectorAll('.nav-item').forEach(function(item) {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 767) {
                closeSidebar();
            }
        });
    });

    // Handle resize: if desktop, always reset sidebar state
    window.addEventListener('resize', function() {
        if (window.innerWidth > 767) {
            closeSidebar();
        }
    });
})();
</script>

<!-- Dark Mode Toggle Controller -->
<script>
(function() {
    const toggleBtn = document.getElementById('themeToggleBtn');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('jurnal_theme', newTheme);
    });
})();
</script>

<!-- Global Tom Select Searchable Dropdown Initializer -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi otomatis semua select dengan class .select-search atau data-searchable="true"
    document.querySelectorAll('select.select-search, select[data-searchable="true"]').forEach(function (el) {
        if (!el.tomselect) {
            new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: el.getAttribute('placeholder') || '-- Ketik untuk mencari / memilih --',
                allowEmptyOption: true,
                maxOptions: 200,
                onChange: function(value) {
                    // Trigger native change event agar listener lain (onchange / Alpine / Vanilla) tetap berjalan
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }
    });
});
</script>

@stack('scripts')
</body>
</html>