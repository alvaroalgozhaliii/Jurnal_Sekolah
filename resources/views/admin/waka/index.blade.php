@extends('layouts.app')

@section('title', 'Data Waka & Pejabat Sekolah — Jurnal Sekolah')
@section('page-title', 'Data Waka & Pejabat Sekolah')

@section('content')
<style>
.stat-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
}
.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon svg { width: 22px; height: 22px; }
.stat-content { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
.stat-value { font-size: 24px; font-weight: 800; color: var(--text-primary); margin-top: 2px; }

/* Grid Waka Cards */
.waka-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.waka-card {
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
}
.waka-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}
.waka-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
}
.waka-role-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.waka-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}
.waka-bidang {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 12px;
}
.waka-tugas {
    font-size: 12.5px;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 16px;
    min-height: 38px;
}
.waka-person-box {
    background: var(--bg-body, rgba(0,0,0,0.02));
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.waka-avatar {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
}
.waka-person-info {
    flex: 1;
    min-width: 0;
}
.waka-person-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}
.waka-person-nip {
    font-size: 12px;
    color: var(--text-muted);
    font-family: monospace;
}
.waka-sk-hint {
    font-size: 11.5px;
    color: var(--text-muted);
    background: rgba(59, 130, 246, 0.05);
    border: 1px dashed rgba(59, 130, 246, 0.25);
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Modal Custom */
.custom-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 16px;
}
.custom-modal-backdrop.show { display: flex; }
.custom-modal {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 520px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.custom-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card-header);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.custom-modal-body { padding: 20px; }
.custom-modal-footer {
    padding: 14px 20px;
    border-top: 1px solid var(--border);
    background: var(--bg-card-header);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div>
        <h1 class="page-title">Data Waka & Pejabat Sekolah</h1>
        <p class="page-subtitle">Kelola struktur pimpinan, Wakil Kepala Sekolah (Waka), dan penugasan SK Resmi SMKN 1 Boyolangu</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <form action="{{ route('admin.waka.sync-sk') }}" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menyinkronkan seluruh jabatan Waka dan Kepala Sekolah sesuai dokumen SK 2026/2027?');">
            @csrf
            <button type="submit" class="btn btn-primary" style="background:#059669; border-color:#059669;">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Sinkronkan dari SK Resmi
            </button>
        </form>
        <a href="{{ asset('csv/sk_tugas_tambahan_2026_2027.csv') }}" class="btn btn-secondary" download>
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Unduh SK Tugas (CSV)
        </a>
        <a href="{{ route('admin.waka.export-csv') }}" class="btn btn-outline">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export Live CSV
        </a>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div style="margin-bottom: 20px; padding: 14px 18px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; color: #059669; display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="margin-bottom: 20px; padding: 14px 18px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; color: #dc2626; display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- Statistik Ringkas -->
<div class="stat-summary-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Waka Terisi</span>
            <span class="stat-value">{{ $totalAssignedWaka }} / {{ $totalWaka }}</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Kepala Sekolah</span>
            <span class="stat-value" style="font-size:18px; color: {{ $isKepalaAssigned ? '#059669' : '#dc2626' }};">
                {{ $isKepalaAssigned ? 'Aktif' : 'Belum Ditugaskan' }}
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Personil Tugas Tambahan SK</span>
            <span class="stat-value">33 Personil</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Tahun Pelajaran</span>
            <span class="stat-value" style="font-size: 20px;">{{ $tahunAktif->tahun ?? '2026/2027' }}</span>
        </div>
    </div>
</div>

<!-- Section: Pimpinan Utama & Waka -->
<div style="margin-bottom: 16px; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin:0;">Struktur Pimpinan & Waka Sekolah</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin: 2px 0 0 0;">Penugasan 1 Kepala Sekolah dan 5 Wakil Kepala Sekolah yang terhubung dengan hak akses sistem</p>
    </div>
</div>

<div class="waka-cards-grid">
    @foreach($wakaRoles as $roleKey => $meta)
        @php
            $data = $assignedPejabat[$roleKey];
            $user = $data['user'];
            $guru = $data['guru'];
            $isAssigned = !empty($user);
            $initial = $guru ? strtoupper(substr($guru->nama, 0, 1)) : ($user ? strtoupper(substr($user->nama, 0, 1)) : '?');
        @endphp
        <div class="waka-card">
            <div>
                <div class="waka-card-header">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ $meta['bg_light'] }}; color: {{ $meta['color'] }}; display: flex; align-items: center; justify-content: center;">
                        @if($meta['icon'] === 'award')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        @elseif($meta['icon'] === 'book-open')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        @elseif($meta['icon'] === 'users')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        @elseif($meta['icon'] === 'user-check')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        @elseif($meta['icon'] === 'tool')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        @endif
                    </div>
                    <span class="waka-role-badge" style="background: {{ $isAssigned ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $isAssigned ? '#059669' : '#dc2626' }};">
                        {{ $isAssigned ? 'Aktif & Terhubung' : 'Belum Ditugaskan' }}
                    </span>
                </div>

                <h3 class="waka-title">{{ $meta['title'] }}</h3>
                <div class="waka-bidang">{{ $meta['bidang'] }}</div>
                <div class="waka-tugas">{{ $meta['tugas'] }}</div>

                <div class="waka-person-box">
                    <div class="waka-avatar" style="background: {{ $meta['color'] }};">
                        {{ $initial }}
                    </div>
                    <div class="waka-person-info">
                        @if($isAssigned)
                            <div class="waka-person-name" title="{{ $guru->nama ?? $user->nama }}">
                                {{ $guru->nama ?? $user->nama }}
                            </div>
                            <div class="waka-person-nip">
                                NIP: {{ $guru->nip ?? ($user->nip ?: '-') }}
                            </div>
                            @if($user->no_hp || ($guru && $guru->no_telp))
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                    WA: {{ $user->no_hp ?: $guru->no_telp }}
                                </div>
                            @endif
                        @else
                            <div class="waka-person-name" style="color: var(--text-muted); font-style: italic;">
                                Belum Ada Pejabat
                            </div>
                            <div class="waka-person-nip" style="color: var(--text-muted);">
                                Klik tombol di bawah untuk menugaskan guru
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SK Official Reference -->
                <div class="waka-sk-hint">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span><strong>SK Resmi:</strong> {{ $meta['sk_nama'] }} ({{ $meta['sk_nip'] }})</span>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-outline" style="width: 100%; font-size: 13px;" 
                    onclick="openWakaModal('{{ $roleKey }}', '{{ $meta['title'] }}', '{{ $guru->id_guru ?? '' }}', '{{ $user->no_hp ?? ($guru->no_telp ?? '') }}')">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    {{ $isAssigned ? 'Ubah Pejabat / No. WA' : 'Tugaskan Guru' }}
                </button>
            </div>
        </div>
    @endforeach
</div>

<!-- Section: Seluruh Tugas Tambahan SK 2026/2027 -->
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 class="card-title" style="margin:0;">Seluruh Penugasan Tugas Tambahan SK 2026/2027</h2>
            <p style="font-size:12.5px; color:var(--text-muted); margin:2px 0 0 0;">Daftar lengkap 33 personil penugasan Kepala Sekolah, Waka, Staf Bidang, Kepala Konsentrasi Keahlian, Kepala Perpustakaan, dan Bendahara</p>
        </div>
        <form action="{{ route('admin.waka.index') }}" method="GET" style="display:flex; gap:8px; margin:0;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, NIP, jabatan..." value="{{ request('search') }}" style="width:240px; height:38px; font-size:13px;">
            <button type="submit" class="btn btn-secondary" style="height:38px; padding:0 14px;">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.waka.index') }}" class="btn btn-outline" style="height:38px; padding:0 12px;">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align:center;">No</th>
                        <th>Nama Pejabat & Gelar</th>
                        <th>NIP</th>
                        <th>Jabatan / Tugas Tambahan</th>
                        <th style="text-align:center;">Tahun Pelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skPersonil as $item)
                        <tr>
                            <td style="text-align:center; font-weight:600;">{{ $item['no'] }}</td>
                            <td>
                                <strong style="color:var(--text-primary);">{{ $item['nama'] }}</strong>
                            </td>
                            <td>
                                <span style="font-family:monospace; font-size:12.5px; color:var(--text-secondary);">{{ $item['nip'] }}</span>
                            </td>
                            <td>
                                @if(str_contains($item['jabatan'], 'Kepala Sekolah'))
                                    <span class="badge" style="background:rgba(124, 58, 237, 0.12); color:#7c3aed; font-weight:700;">{{ $item['jabatan'] }}</span>
                                @elseif(str_contains($item['jabatan'], 'Waka'))
                                    <span class="badge" style="background:rgba(37, 99, 235, 0.12); color:#2563eb; font-weight:700;">{{ $item['jabatan'] }}</span>
                                @elseif(str_contains($item['jabatan'], 'Ketua Konsentrasi'))
                                    <span class="badge" style="background:rgba(16, 185, 129, 0.12); color:#059669;">{{ $item['jabatan'] }}</span>
                                @elseif(str_contains($item['jabatan'], 'Bendahara'))
                                    <span class="badge" style="background:rgba(245, 158, 11, 0.12); color:#d97706;">{{ $item['jabatan'] }}</span>
                                @else
                                    <span class="badge" style="background:rgba(100, 116, 139, 0.12); color:var(--text-primary);">{{ $item['jabatan'] }}</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="badge badge-outline">{{ $item['tahun_pelajaran'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">
                                Tidak ada data tugas tambahan yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Custom Modal Ubah Pejabat -->
<div id="wakaModalBackdrop" class="custom-modal-backdrop">
    <div class="custom-modal">
        <form id="wakaModalForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div class="custom-modal-header">
                <h3 id="wakaModalTitle" style="font-size:16px; font-weight:700; margin:0; color:var(--text-primary);">
                    Tugaskan Pejabat
                </h3>
                <button type="button" onclick="closeWakaModal()" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);">&times;</button>
            </div>

            <div class="custom-modal-body">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Pilih Guru yang Ditugaskan</label>
                    <select name="id_guru" id="modalSelectGuru" class="form-control" style="width:100%;">
                        <option value="">-- Kosongkan Penugasan (Tidak Ada Pejabat) --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}">
                                {{ $g->nama }} (NIP: {{ $g->nip ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
                        Guru yang dipilih akan secara otomatis mendapatkan hak akses pimpinan/waka pada akun login miliknya.
                    </small>
                </div>

                <div class="form-group" style="margin-bottom:8px;">
                    <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Nomor WhatsApp / HP Pejabat (Opsional)</label>
                    <input type="text" name="no_hp" id="modalInputNoHp" class="form-control" placeholder="Contoh: 081234567890">
                    <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
                        Digunakan untuk notifikasi gateway WhatsApp pengajuan izin & dispensasi.
                    </small>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeWakaModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

<script>
let wakaTomSelect = null;

document.addEventListener('DOMContentLoaded', function () {
    const selectElem = document.getElementById('modalSelectGuru');
    if (typeof TomSelect !== 'undefined' && selectElem) {
        wakaTomSelect = new TomSelect(selectElem, {
            create: false,
            placeholder: 'Ketik nama atau NIP guru...',
            dropdownParent: 'body'
        });
    }
});

function openWakaModal(roleKey, roleTitle, currentGuruId, currentNoHp) {
    const form = document.getElementById('wakaModalForm');
    const title = document.getElementById('wakaModalTitle');
    const noHpInput = document.getElementById('modalInputNoHp');

    form.action = "{{ url('admin/waka') }}/" + roleKey;
    title.innerText = "Tugaskan: " + roleTitle;
    noHpInput.value = currentNoHp || '';

    if (wakaTomSelect) {
        wakaTomSelect.setValue(currentGuruId || '');
    } else {
        document.getElementById('modalSelectGuru').value = currentGuruId || '';
    }

    document.getElementById('wakaModalBackdrop').classList.add('show');
}

function closeWakaModal() {
    document.getElementById('wakaModalBackdrop').classList.remove('show');
}

// Close on backdrop click
document.getElementById('wakaModalBackdrop').addEventListener('click', function (e) {
    if (e.target === this) {
        closeWakaModal();
    }
});
</script>
@endsection