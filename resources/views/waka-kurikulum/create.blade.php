@extends('layouts.app')

@section('title', 'Buat Jadwal Piket Bulanan — Jurnal Sekolah')
@section('page-title', 'Buat Jadwal Piket Bulanan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Buat Jadwal Piket Bulanan</h1>
        <p class="page-subtitle">Pilih bulan dan tentukan penugasan Waka &amp; Guru Piket untuk setiap hari</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-16">
    <ul style="margin:0; padding-left:16px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('waka-kurikulum.jadwal.store') }}" method="POST" id="formBulanan">
    @csrf

    {{-- Filter Periode Bulan & Tahun --}}
    <div class="card mb-16">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Pilih Periode Bulan &amp; Tahun
            </h3>
        </div>
        <div class="card-body">
            <div class="form-row" style="gap:16px; max-width: 600px;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="bulan">Bulan <span class="req">*</span></label>
                    <select class="form-control" id="bulan" name="bulan" required>
                        @php
                            $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            $selectedBulan = old('bulan', date('n'));
                        @endphp
                        @foreach($bulanList as $i => $nama)
                            <option value="{{ $i+1 }}" @selected($selectedBulan == $i+1)>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="tahun">Tahun <span class="req">*</span></label>
                    <select class="form-control" id="tahun" name="tahun" required>
                        @php $selectedTahun = old('tahun', date('Y')); @endphp
                        @for($y = date('Y')-1; $y <= date('Y')+2; $y++)
                            <option value="{{ $y }}" @selected($selectedTahun == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Penugasan Harian --}}
    <div class="card">
        <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <h3 class="card-title">
                <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
                Daftar Penugasan Harian — <span id="labelBulanAktif" style="color:var(--navy-primary); font-weight:700;"></span>
            </h3>
            <span class="text-muted" style="font-size:12px;">
                🔍 Klik pada kolom Waka atau Guru untuk mencari dan memilih nama dengan cepat
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper" style="border:none; border-radius:0; overflow-x:auto;">
                <table class="table" style="min-width:820px;">
                    <thead>
                        <tr>
                            <th style="width:45px; text-align:center;">No</th>
                            <th style="width:110px;">Hari</th>
                            <th style="width:140px;">Tanggal</th>
                            <th style="min-width:260px;">Waka Bertugas <span class="req">*</span></th>
                            <th style="min-width:280px;">Guru Piket (Opsional)</th>
                            <th style="min-width:180px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="tabelHari">
                        <!-- Baris dirender oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg" style="font-weight:700; padding:10px 24px;">
                💾 SIMPAN JADWAL BULANAN
            </button>
            <a href="{{ route('waka-kurikulum.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            <span class="text-muted" style="font-size:12px; margin-left:auto;">
                ⚠️ Tanggal yang sudah ada jadwalnya akan dilewati otomatis.
            </span>
        </div>
    </div>
</form>

{{-- Searchable Dropdown Floating Modal --}}
<div id="searchablePicker" class="custom-picker-popover" style="display:none;">
    <div class="picker-search-box">
        <svg class="picker-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="pickerSearchInput" placeholder="Ketik untuk mencari nama / mapel..." autocomplete="off">
        <button type="button" id="pickerCloseBtn" class="picker-close-btn">&times;</button>
    </div>
    <div class="picker-options-list" id="pickerOptionsList">
        <!-- List opsi dibuat dinamis -->
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom Searchable Picker Trigger */
.picker-trigger {
    width: 100%;
    min-height: 38px;
    padding: 7px 12px;
    border: 1px solid var(--border);
    border-radius: 6px;
    background-color: var(--bg-card);
    color: var(--text-primary);
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    cursor: pointer;
    text-align: left;
    transition: var(--transition);
}
.picker-trigger:hover {
    border-color: #3b82f6;
    background-color: rgba(59, 130, 246, 0.05);
}
.picker-trigger.active {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.picker-trigger .trigger-text {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.picker-trigger .trigger-placeholder {
    color: var(--text-muted);
}
.picker-trigger .trigger-icon {
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    flex-shrink: 0;
    transition: transform 0.2s;
}

/* Floating Popover Container */
.custom-picker-popover {
    position: fixed;
    z-index: 10000;
    width: 320px;
    max-width: 90vw;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15), 0 4px 10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: pickerFadeIn 0.15s ease-out;
}
@keyframes pickerFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

[data-theme="dark"] .custom-picker-popover {
    background: #0f172a;
    border-color: #1e293b;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6);
}

.picker-search-box {
    padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
}
[data-theme="dark"] .picker-search-box {
    border-bottom-color: #1e293b;
    background: #131c31;
}

.picker-search-icon {
    width: 16px;
    height: 16px;
    color: #64748b;
    flex-shrink: 0;
}
[data-theme="dark"] .picker-search-icon {
    color: #94a3b8;
}

#pickerSearchInput {
    flex-grow: 1;
    border: none !important;
    background: transparent !important;
    padding: 0 !important;
    font-size: 13.5px !important;
    color: inherit !important;
    outline: none !important;
    box-shadow: none !important;
}

.picker-close-btn {
    background: none;
    border: none;
    font-size: 18px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 0 4px;
}
.picker-close-btn:hover {
    color: #ef4444;
}

.picker-options-list {
    max-height: 240px;
    overflow-y: auto;
    padding: 6px 0;
}
.picker-option-item {
    padding: 9px 14px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}
[data-theme="dark"] .picker-option-item {
    border-bottom-color: #1a2438;
}
.picker-option-item:last-child {
    border-bottom: none;
}
.picker-option-item:hover, .picker-option-item.selected {
    background: #eff6ff;
}
[data-theme="dark"] .picker-option-item:hover, [data-theme="dark"] .picker-option-item.selected {
    background: #162035;
}

.picker-option-title {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
}
[data-theme="dark"] .picker-option-title {
    color: #f8fafc;
}

.picker-option-sub {
    font-size: 11.5px;
    color: #64748b;
}
[data-theme="dark"] .picker-option-sub {
    color: #94a3b8;
}

.picker-no-results {
    padding: 24px 16px;
    text-align: center;
    color: var(--text-muted);
    font-size: 12.5px;
}
</style>
@endpush

@push('scripts')
@php
    $wakasJson = $wakas->map(function($w) {
        return [
            'id'   => $w->id_user,
            'nama' => $w->nama,
            'sub'  => strtoupper(str_replace('_', ' ', $w->role)),
        ];
    })->values()->toArray();

    $gurusJson = $gurus->map(function($g) {
        return [
            'id'   => $g->id_guru,
            'nama' => $g->nama,
            'sub'  => $g->bidang_studi ?? ($g->nip ? 'NIP: '.$g->nip : 'Guru Piket'),
        ];
    })->values()->toArray();
@endphp
<script>
const wakas = @json($wakasJson);
const gurus = @json($gurusJson);
const existingDates = @json($existingDates ?? []);

const hariNama  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

let activePickerTarget = null; // { type: 'waka'|'guru', dateStr: '...', inputEl, triggerEl }

function renderTabel() {
    const bulan = parseInt(document.getElementById('bulan').value);
    const tahun = parseInt(document.getElementById('tahun').value);
    const jumlahHari = new Date(tahun, bulan, 0).getDate();

    document.getElementById('labelBulanAktif').textContent = bulanNama[bulan-1] + ' ' + tahun;

    let html = '';
    let no = 1;
    for (let d = 1; d <= jumlahHari; d++) {
        const dateObj = new Date(tahun, bulan - 1, d);
        const dayIdx  = dateObj.getDay();
        const dateStr = `${tahun}-${String(bulan).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const sudahAda  = existingDates.includes(dateStr);
        const isWeekend = dayIdx === 0 || dayIdx === 6;

        let rowStyle = '';
        let badge    = '';
        if (sudahAda) {
            rowStyle = 'background:rgba(100,116,139,0.08);';
            badge    = '<span class="badge" style="background:#64748b;color:#fff;font-size:10px;margin-left:4px;">Sudah Ada</span>';
        } else if (isWeekend) {
            rowStyle = 'background:rgba(245,158,11,0.06);';
            badge    = '<span class="badge" style="background:rgba(245,158,11,0.18);color:#d97706;font-size:10px;margin-left:4px;">Weekend</span>';
        }

        html += `
        <tr style="${rowStyle}">
            <td style="color:var(--text-muted); text-align:center;">${no++}</td>
            <td>
                <span style="font-weight:${isWeekend?'700':'500'}; color:${isWeekend?'#d97706':'inherit'};">
                    ${hariNama[dayIdx]}
                </span>
            </td>
            <td style="font-weight:600;">
                ${String(d).padStart(2,'0')} ${bulanNama[bulan-1]} ${tahun}
                ${badge}
                ${sudahAda ? '' : `<input type="hidden" name="hari[]" value="${dateStr}">`}
            </td>
            <td>
                ${sudahAda
                    ? '<em class="text-muted" style="font-size:12px;">Sudah terisi</em>'
                    : `
                    <input type="hidden" name="id_user_waka[${dateStr}]" id="waka_val_${dateStr}" value="">
                    <button type="button" class="picker-trigger" id="waka_btn_${dateStr}" onclick="openPicker(event, 'waka', '${dateStr}')">
                        <span class="trigger-text trigger-placeholder" id="waka_txt_${dateStr}">${isWeekend ? '-- Kosongkan (Weekend) --' : '-- Pilih Waka Bertugas --'}</span>
                        <svg class="trigger-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    `
                }
            </td>
            <td>
                ${sudahAda
                    ? '<em class="text-muted" style="font-size:12px;">Sudah terisi</em>'
                    : `
                    <input type="hidden" name="id_guru_piket[${dateStr}]" id="guru_val_${dateStr}" value="">
                    <button type="button" class="picker-trigger" id="guru_btn_${dateStr}" onclick="openPicker(event, 'guru', '${dateStr}')">
                        <span class="trigger-text trigger-placeholder" id="guru_txt_${dateStr}">-- Pilih Guru Piket (Opsional) --</span>
                        <svg class="trigger-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    `
                }
            </td>
            <td>
                ${sudahAda
                    ? ''
                    : `<input type="text" class="form-control form-control-sm" name="keterangan[${dateStr}]" placeholder="${isWeekend ? 'Libur Weekend' : 'Keterangan...'}" maxlength="100">`
                }
            </td>
        </tr>`;
    }
    document.getElementById('tabelHari').innerHTML = html;
}

// Buka Popover Searchable Picker
function openPicker(event, type, dateStr) {
    event.stopPropagation();
    closePicker();

    const triggerEl = document.getElementById(`${type}_btn_${dateStr}`);
    const inputEl   = document.getElementById(`${type}_val_${dateStr}`);
    const textEl    = document.getElementById(`${type}_txt_${dateStr}`);
    const popoverEl = document.getElementById('searchablePicker');
    const searchInput = document.getElementById('pickerSearchInput');

    activePickerTarget = { type, dateStr, triggerEl, inputEl, textEl };
    triggerEl.classList.add('active');

    // Hitung posisi popover relatif terhadap tombol trigger
    const rect = triggerEl.getBoundingClientRect();
    const popoverWidth = 320;
    let left = rect.left;
    if (left + popoverWidth > window.innerWidth - 10) {
        left = window.innerWidth - popoverWidth - 10;
    }
    let top = rect.bottom + 4;
    // Jika tidak cukup ruang ke bawah, letakkan di atas tombol
    if (top + 260 > window.innerHeight && rect.top > 260) {
        top = rect.top - 264;
    }

    popoverEl.style.left = `${Math.max(10, left)}px`;
    popoverEl.style.top  = `${top}px`;
    popoverEl.style.display = 'flex';

    searchInput.value = '';
    searchInput.placeholder = type === 'waka' ? 'Cari Waka (nama / jabatan)...' : 'Cari Guru (nama / mapel)...';
    renderPickerOptions();
    setTimeout(() => searchInput.focus(), 50);
}

// Render daftar opsi pada picker
function renderPickerOptions() {
    if (!activePickerTarget) return;

    const listEl = document.getElementById('pickerOptionsList');
    const filterText = document.getElementById('pickerSearchInput').value.toLowerCase().trim();
    const items = activePickerTarget.type === 'waka' ? wakas : gurus;
    const currentVal = activePickerTarget.inputEl.value;

    let html = '';

    // Opsi kosongkan untuk Waka (misal libur/tidak ada piket) maupun Guru
    html += `
    <div class="picker-option-item ${currentVal === '' ? 'selected' : ''}" onclick="selectPickerItem('', '')">
        <span class="picker-option-title" style="color:var(--text-muted); font-style:italic;">-- Kosongkan (Tidak Ada Piket / Libur) --</span>
    </div>`;

    let matchCount = 0;
    items.forEach(item => {
        const titleMatch = item.nama.toLowerCase().includes(filterText);
        const subMatch   = item.sub.toLowerCase().includes(filterText);

        if (!filterText || titleMatch || subMatch) {
            matchCount++;
            const isSelected = String(currentVal) === String(item.id);
            html += `
            <div class="picker-option-item ${isSelected ? 'selected' : ''}" onclick="selectPickerItem('${item.id}', '${item.nama} (${item.sub})')">
                <span class="picker-option-title">${item.nama}</span>
                <span class="picker-option-sub">${item.sub}</span>
            </div>`;
        }
    });

    if (matchCount === 0) {
        html += `<div class="picker-no-results">Tidak ada nama yang cocok dengan "${filterText}"</div>`;
    }

    listEl.innerHTML = html;
}

// Pilih item dari picker
function selectPickerItem(id, displayLabel) {
    if (!activePickerTarget) return;

    activePickerTarget.inputEl.value = id;
    if (id) {
        activePickerTarget.textEl.textContent = displayLabel;
        activePickerTarget.textEl.classList.remove('trigger-placeholder');
        activePickerTarget.textEl.style.fontWeight = '600';
        activePickerTarget.textEl.style.color = 'var(--text-primary)';
    } else {
        activePickerTarget.textEl.textContent = activePickerTarget.type === 'waka' ? '-- Pilih Waka Bertugas --' : '-- Pilih Guru Piket (Opsional) --';
        activePickerTarget.textEl.classList.add('trigger-placeholder');
        activePickerTarget.textEl.style.fontWeight = '400';
    }

    closePicker();
}

// Tutup Popover Picker
function closePicker() {
    const popoverEl = document.getElementById('searchablePicker');
    if (popoverEl) popoverEl.style.display = 'none';
    if (activePickerTarget && activePickerTarget.triggerEl) {
        activePickerTarget.triggerEl.classList.remove('active');
    }
    activePickerTarget = null;
}

// Filter saat mengetik di search box
document.getElementById('pickerSearchInput').addEventListener('input', renderPickerOptions);
document.getElementById('pickerCloseBtn').addEventListener('click', closePicker);

// Klik di luar menutup picker
document.addEventListener('click', function(e) {
    const popoverEl = document.getElementById('searchablePicker');
    if (popoverEl && popoverEl.style.display !== 'none') {
        if (!popoverEl.contains(e.target) && !e.target.closest('.picker-trigger')) {
            closePicker();
        }
    }
});

// Escape key menutup picker
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePicker();
});

// Render awal saat halaman dibuka
renderTabel();

// Re-render saat pilihan bulan / tahun diganti
document.getElementById('bulan').addEventListener('change', () => { closePicker(); renderTabel(); });
document.getElementById('tahun').addEventListener('change', () => { closePicker(); renderTabel(); });
</script>
@endpush
