@extends('layouts.app')

@section('title', 'Catat Siswa Sakit — Jurnal Sekolah')
@section('page-title', 'Catat Siswa Sakit')

@section('content')
<style>
/* ═══════════════════════════════════════════════════
   AESTHETIC & ELEGANT STYLING - CATAT SISWA SAKIT
   ═══════════════════════════════════════════════════ */
.sakit-layout {
    display: grid;
    grid-template-columns: minmax(360px, 450px) 1fr;
    gap: 24px;
    align-items: start;
}

@media (min-width: 993px) {
    /* Riwayat Pencatatan Sakit tetap diam (sticky) saat formulir di-scroll */
    .sakit-history-card {
        position: sticky;
        top: 16px;
        align-self: start;
        z-index: 10;
    }

    /* Formulir Siswa Sakit memiliki scroll independen yang rapi & estetik */
    .sakit-form-scrollable {
        max-height: calc(100vh - 250px);
        min-height: 420px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 22px;
        scrollbar-width: thin;
        scrollbar-color: rgba(239, 68, 68, 0.3) transparent;
    }

    .sakit-form-scrollable::-webkit-scrollbar {
        width: 6px;
    }

    .sakit-form-scrollable::-webkit-scrollbar-track {
        background: transparent;
    }

    .sakit-form-scrollable::-webkit-scrollbar-thumb {
        background-color: rgba(239, 68, 68, 0.3);
        border-radius: 10px;
    }

    .sakit-form-scrollable::-webkit-scrollbar-thumb:hover {
        background-color: rgba(239, 68, 68, 0.55);
    }
}

@media (max-width: 992px) {
    .sakit-layout {
        grid-template-columns: 1fr;
    }

    .sakit-form-scrollable {
        padding: 22px;
    }
}

/* Stat Cards */
.sakit-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.sakit-stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}


.sakit-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sakit-stat-icon.red {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}

.sakit-stat-icon.amber {
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
}

.sakit-stat-icon.blue {
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
}

.sakit-stat-icon svg {
    width: 22px;
    height: 22px;
}

.sakit-stat-val {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
    color: var(--text-primary);
    font-family: 'Inter', sans-serif;
}

.sakit-stat-lbl {
    font-size: 11.5px;
    color: var(--text-secondary);
    font-weight: 500;
    margin-top: 2px;
}

/* Elegant Card Styling */
.sakit-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.sakit-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-card-header, rgba(255, 255, 255, 0.02));
}

.sakit-card-title-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sakit-badge-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(249, 115, 22, 0.2));
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #f87171;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sakit-badge-icon svg {
    width: 18px;
    height: 18px;
}

.sakit-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.3;
}

.sakit-card-subtitle {
    font-size: 11.5px;
    color: var(--text-secondary);
    margin-top: 2px;
}

.sakit-card-body {
    padding: 22px;
}

/* Quick Tags / Chips */
.quick-tags-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
}

.quick-tags-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    margin-right: 2px;
}

.quick-tag {
    background: var(--badge-gray-bg, #f1f5f9);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    font-size: 11px;
    font-weight: 500;
    padding: 3px 9px;
    border-radius: 999px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.quick-tag:hover {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.3);
    color: #ef4444;
    transform: translateY(-1px);
}

[data-theme="dark"] .quick-tag {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: #94a3b8;
}

[data-theme="dark"] .quick-tag:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fca5a5;
}

/* Aesthetic File Upload Dropzone */
.file-upload-aesthetic {
    position: relative;
    border: 1.5px dashed var(--border);
    border-radius: 10px;
    padding: 16px;
    text-align: center;
    background: var(--bg-card-header, rgba(0, 0, 0, 0.01));
    transition: all 0.2s ease;
    cursor: pointer;
}

.file-upload-aesthetic:hover {
    border-color: #f87171;
    background: rgba(239, 68, 68, 0.03);
}

.file-input-hidden {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 5;
}

.file-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    margin: 0;
}

.upload-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2px;
}

.upload-text-main {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-primary);
}

.upload-text-sub {
    font-size: 11px;
    color: var(--text-muted);
}

.file-preview-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px 12px;
    margin-top: 10px;
    position: relative;
    z-index: 10;
}

.file-preview-box img {
    width: 42px;
    height: 42px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid var(--border);
}

.file-preview-info {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
}

.file-preview-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 190px;
}

.btn-remove-file {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
}

.btn-remove-file:hover {
    background: rgba(239, 68, 68, 0.1);
}

/* Aesthetic Submit Button */
.btn-sakit-submit {
    width: 100%;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 13.5px;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: 18px;
}

.btn-sakit-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.45);
    background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
}

.btn-sakit-submit:active {
    transform: translateY(0);
}

.btn-sakit-submit svg {
    width: 17px;
    height: 17px;
}

/* History Student Avatar */
.student-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}

.student-info-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Modal Lightbox for Proof Photo */
.img-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeInModal 0.2s ease;
}

.img-modal-backdrop.show {
    display: flex;
}

.img-modal-dialog {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    max-width: 580px;
    width: 100%;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    animation: scaleInModal 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.img-modal-header {
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.img-modal-close {
    background: transparent;
    border: none;
    font-size: 20px;
    line-height: 1;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 4px;
}

.img-modal-close:hover {
    color: #ef4444;
}

.img-modal-body {
    padding: 16px;
    text-align: center;
    background: rgba(0, 0, 0, 0.05);
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleInModal {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

{{-- Page Header --}}
<div class="page-header" style="margin-bottom: 20px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 9999px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align: -1px; margin-right: 4px;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                UKS & Layanan Piket
            </span>
        </div>
        <h1 class="page-title">Pencatatan Siswa Sakit</h1>
        <p class="page-subtitle">Pencatatan medis darurat bagi siswa yang beristirahat di UKS atau dipulangkan saat jam KBM</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 8px;">
        <a href="{{ route('piket.dashboard') }}" class="btn btn-secondary" style="font-size: 12.5px; padding: 7px 14px; font-weight: 600;">
            &larr; Dashboard Piket
        </a>
    </div>
</div>

{{-- Summary Stats Pills --}}
@php
    $todayDateStr = date('Y-m-d');
    $sakitHariIni = $riwayatSakit->filter(function($item) use ($todayDateStr) {
        return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') === $todayDateStr;
    })->count();
@endphp

<div class="sakit-stats-grid mb-24">
    <div class="sakit-stat-card" style="border-left: 4px solid #dc2626;">
        <div class="sakit-stat-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        </div>
        <div>
            <div class="sakit-stat-val">{{ $sakitHariIni }}</div>
            <div class="sakit-stat-lbl">Siswa Sakit Hari Ini</div>
        </div>
    </div>
    <div class="sakit-stat-card" style="border-left: 4px solid #d97706;">
        <div class="sakit-stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        </div>
        <div>
            <div class="sakit-stat-val">{{ $riwayatSakit->count() }}</div>
            <div class="sakit-stat-lbl">Total Riwayat Sakit</div>
        </div>
    </div>
    <div class="sakit-stat-card" style="border-left: 4px solid #0284c7;">
        <div class="sakit-stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div>
            <div class="sakit-stat-val">{{ $siswas->count() }}</div>
            <div class="sakit-stat-lbl">Siswa Aktif Terdaftar</div>
        </div>
    </div>
</div>

{{-- Notifications --}}
@if(session('success'))
<div class="alert alert-success mb-20" style="display:flex; align-items:center; gap:10px;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    <div>{{ session('success') }}</div>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-20">
    <div style="font-weight:600; margin-bottom:4px;">Terdapat isian formulir yang belum valid:</div>
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Main Grid: Form + Riwayat --}}
<div class="sakit-layout">

    {{-- LEFT: FORM CARD --}}
    <div class="sakit-card sakit-form-card">
        <div class="sakit-card-header">
            <div class="sakit-card-title-group">
                <div class="sakit-badge-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </div>
                <div>
                    <h2 class="sakit-card-title">Formulir Siswa Sakit</h2>
                    <div class="sakit-card-subtitle">Input data penanganan siswa saat ini</div>
                </div>
            </div>
            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #f87171; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                Langsung Terverifikasi
            </span>
        </div>
        <div class="sakit-form-scrollable">
            <form action="{{ route('piket.anak-sakit.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Pilih Siswa --}}
                <div class="form-group mb-16">
                    <label class="form-label" for="id_siswa" style="display:flex; align-items:center; justify-content:space-between;">
                        <span>Pilih Siswa <span class="req">*</span></span>
                        <span style="font-size:11px; font-weight:normal; color:var(--text-muted);">Ketik nama / kelas</span>
                    </label>
                    <select id="id_siswa" name="id_siswa" class="form-control select-search" data-searchable="true" required placeholder="Ketik untuk mencari siswa">
                        <option value="">Ketik untuk mencari siswa</option>
                        @foreach($siswas as $s)
                        <option value="{{ $s->id_siswa }}" {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama }} (Kelas {{ $s->kelas->nama_kelas ?? '-' }} • NISN: {{ $s->NISN ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="form-group mb-16">
                    <label class="form-label" for="tanggal">Tanggal Pemeriksaan / Sakit <span class="req">*</span></label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="form-control" required>
                </div>

                {{-- Alasan & Gejala --}}
                <div class="form-group mb-16">
                    <label class="form-label" for="alasan">Alasan / Gejala Sakit <span class="req">*</span></label>
                    <textarea id="alasan" name="alasan" rows="3" class="form-control" required placeholder="Tuliskan keluhan kondisi siswa, penanganan UKS, atau info izin pulang...">{{ old('alasan') }}</textarea>

                    {{-- Quick Fill Chips --}}
                    <div class="quick-tags-wrap">
                        <span class="quick-tags-label">Pilihan cepat:</span>
                        <button type="button" class="quick-tag" onclick="addGejala('Demam tinggi / meriang di UKS')">+ Demam</button>
                        <button type="button" class="quick-tag" onclick="addGejala('Pusing / sakit kepala hebat')">+ Pusing</button>
                        <button type="button" class="quick-tag" onclick="addGejala('Maag / nyeri perut / diare')">+ Sakit Perut</button>
                        <button type="button" class="quick-tag" onclick="addGejala('Mual & muntah-muntah')">+ Mual</button>
                        <button type="button" class="quick-tag" onclick="addGejala('Izin pulang dijemput orang tua')">+ Pulang Dijemput</button>
                    </div>
                </div>

                {{-- Upload Bukti / Foto --}}
                <div class="form-group mb-20">
                    <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                        <span>Foto Bukti / Surat Sakit</span>
                        <span style="font-size:11px; color:var(--text-muted); font-weight:normal;">Opsional</span>
                    </label>

                    <div class="file-upload-aesthetic" id="dropZone">
                        <input type="file" id="lampiran_foto" name="lampiran_foto" accept="image/*" class="file-input-hidden" onchange="previewFoto(this)">
                        <label for="lampiran_foto" class="file-upload-label" id="fileUploadLabel">
                            <div class="upload-icon-circle">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                            <div class="upload-text-main">Pilih atau Seret Foto ke Sini</div>
                            <div class="upload-text-sub">Surat dokter / foto penanganan di UKS (JPG, PNG maks 2MB)</div>
                        </label>
                        <div id="filePreviewWrap" style="display:none;" class="file-preview-box">
                            <img id="filePreviewImg" src="" alt="Preview Bukti">
                            <div class="file-preview-info">
                                <span id="filePreviewName" class="file-preview-name"></span>
                                <button type="button" class="btn-remove-file" onclick="removeFoto()">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-sakit-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    <span>Simpan Catatan Siswa Sakit</span>
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: RIWAYAT TABLE --}}
    <div class="sakit-card sakit-history-card">
        <div class="sakit-card-header">
            <div class="sakit-card-title-group">
                <div class="sakit-badge-icon" style="background: rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3); color: #60a5fa;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div>
                    <h2 class="sakit-card-title">Riwayat Pencatatan Sakit</h2>
                    <div class="sakit-card-subtitle">Daftar siswa yang telah dicatat sakit oleh tim piket</div>
                </div>
            </div>
            <div>
                <input type="text" id="filterRiwayatInput" placeholder="🔍 Cari siswa / kelas..." onkeyup="filterRiwayatTable()" class="form-control" style="font-size: 12px; padding: 6px 12px; width: 180px; height: 32px; border-radius: 8px;">
            </div>
        </div>

        <div class="card-body" style="padding: 0;">
            @if($riwayatSakit->count() > 0)
            <div class="table-wrapper" style="border: none; border-radius: 0; max-height: 580px; overflow-y: auto;">
                <table class="table" id="tableRiwayatSakit">
                    <thead>
                        <tr>
                            <th style="width: 105px;">Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Gejala / Catatan</th>
                            <th style="text-align: center; width: 90px;">Bukti</th>
                            <th style="width: 110px;">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSakit as $r)
                        @php
                            $namaSiswa = $r->siswa->nama ?? 'Siswa';
                            $initial = strtoupper(mb_substr($namaSiswa, 0, 1));
                            $kelasNama = $r->siswa->kelas->nama_kelas ?? '-';
                            $tglFormatted = \Carbon\Carbon::parse($r->tanggal)->format('d M Y');
                            $isToday = (\Carbon\Carbon::parse($r->tanggal)->format('Y-m-d') === date('Y-m-d'));
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 700; font-size: 12px; color: var(--text-primary); white-space: nowrap;">
                                    {{ $tglFormatted }}
                                </div>
                                @if($isToday)
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-top: 2px; display: inline-block;">Hari Ini</span>
                                @endif
                            </td>
                            <td>
                                <div class="student-info-cell">
                                    <div class="student-avatar">{{ $initial }}</div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary); line-height: 1.3;">
                                            {{ $namaSiswa }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            NISN: {{ $r->siswa->NISN ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-navy" style="font-size: 11px; padding: 4px 8px; font-weight: 600;">
                                    {{ $kelasNama }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 12px; color: var(--text-primary); max-width: 260px; line-height: 1.4;">
                                    {{ $r->alasan }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($r->lampiran_foto)
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openImgModal('{{ asset('storage/' . $r->lampiran_foto) }}', '{{ addslashes($namaSiswa) }}')" style="font-size: 11px; padding: 4px 8px; border-radius: 6px; white-space: nowrap;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    Foto
                                </button>
                                @else
                                <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 11.5px; color: var(--text-secondary); font-weight: 500;">
                                    {{ $r->pengaju->nama ?? 'Piket' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state" style="padding: 48px 24px; text-align: center;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Semua Siswa Terpantau Sehat</div>
                <div class="empty-state-text" style="font-size: 12px; color: var(--text-secondary);">Belum ada riwayat siswa sakit yang dicatat oleh tim piket.</div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Lightbox Modal for Photo Bukti --}}
<div id="imagePreviewModal" class="img-modal-backdrop" onclick="closeImgModal(event)">
    <div class="img-modal-dialog">
        <div class="img-modal-header">
            <span id="modalImgTitle" style="font-weight: 700; font-size: 13px; color: var(--text-primary);">Foto Bukti Sakit</span>
            <button type="button" class="img-modal-close" onclick="closeImgModalDirect()">&times;</button>
        </div>
        <div class="img-modal-body">
            <img id="modalPreviewImg" src="" alt="Bukti Foto" style="max-width: 100%; max-height: 70vh; border-radius: 8px; display: block; margin: 0 auto; object-fit: contain;">
        </div>
        <div style="padding: 10px 16px; display: flex; justify-content: flex-end; background: var(--bg-card-header); border-top: 1px solid var(--border);">
            <a id="modalOpenExternal" href="#" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 11.5px;">Buka Gambar Penuh &nearr;</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Quick fill gejala tag into textarea
function addGejala(text) {
    const textarea = document.getElementById('alasan');
    if (!textarea) return;
    if (textarea.value.trim() === '') {
        textarea.value = text;
    } else {
        textarea.value = textarea.value.trim() + ', ' + text;
    }
    textarea.focus();
}

// Preview uploaded photo
function previewFoto(input) {
    const previewWrap = document.getElementById('filePreviewWrap');
    const previewImg = document.getElementById('filePreviewImg');
    const previewName = document.getElementById('filePreviewName');
    const uploadLabel = document.getElementById('fileUploadLabel');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        previewName.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewWrap.style.display = 'flex';
            uploadLabel.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

// Remove uploaded photo
function removeFoto() {
    const input = document.getElementById('lampiran_foto');
    const previewWrap = document.getElementById('filePreviewWrap');
    const uploadLabel = document.getElementById('fileUploadLabel');

    if (input) input.value = '';
    if (previewWrap) previewWrap.style.display = 'none';
    if (uploadLabel) uploadLabel.style.display = 'flex';
}

// Filter Riwayat Table
function filterRiwayatTable() {
    const input = document.getElementById('filterRiwayatInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('tableRiwayatSakit');
    if (!table) return;

    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        const text = rows[i].textContent.toLowerCase();
        if (text.indexOf(filter) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

// Modal Lightbox
function openImgModal(src, studentName) {
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('modalPreviewImg');
    const title = document.getElementById('modalImgTitle');
    const extLink = document.getElementById('modalOpenExternal');

    img.src = src;
    title.textContent = 'Bukti Sakit: ' + studentName;
    extLink.href = src;
    modal.classList.add('show');
}

function closeImgModal(e) {
    if (e.target.id === 'imagePreviewModal') {
        closeImgModalDirect();
    }
}

function closeImgModalDirect() {
    const modal = document.getElementById('imagePreviewModal');
    modal.classList.remove('show');
    document.getElementById('modalPreviewImg').src = '';
}
</script>
@endpush
@endsection
