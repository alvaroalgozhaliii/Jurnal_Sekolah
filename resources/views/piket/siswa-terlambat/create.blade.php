@extends('layouts.app')

@section('title', 'Catat Siswa Terlambat — Jurnal Sekolah')
@section('page-title', 'Catat Siswa Terlambat')

@section('content')
<style>
/* ── Search Siswa ────────────────────────────────── */
.search-wrapper {
    position: relative;
}
.search-input {
    width: 100%;
    padding: 10px 14px 10px 40px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 14px;
    transition: border-color .2s;
}
.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 59,130,246), .15);
}
.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    pointer-events: none;
    width: 16px;
    height: 16px;
}
.search-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    z-index: 200;
    max-height: 280px;
    overflow-y: auto;
    display: none;
}
.search-dropdown.open { display: block; }
.dropdown-item {
    padding: 10px 14px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 2px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.dropdown-item:last-child { border-bottom: none; }
.dropdown-item:hover, .dropdown-item.focused { background: var(--bg-page); }
.dropdown-item .item-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}
.dropdown-item .item-meta {
    font-size: 12px;
    color: var(--text-secondary);
}
.dropdown-empty {
    padding: 20px 14px;
    text-align: center;
    color: var(--text-secondary);
    font-size: 13px;
}
/* ── Selected Siswa Card ─────────────────────────── */
.selected-card {
    background: linear-gradient(135deg, rgba(30,58,138,.08), rgba(59,130,246,.08));
    border: 1.5px solid var(--primary);
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 8px;
    animation: fadeIn .25s ease;
}
.selected-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    flex-shrink: 0;
}
.selected-info .sname { font-weight: 700; color: var(--text-primary); font-size: 15px; }
.selected-info .smeta { font-size: 12px; color: var(--text-secondary); }
.selected-clear {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-secondary);
    font-size: 18px;
    line-height: 1;
    padding: 4px 6px;
    border-radius: 6px;
    transition: background .15s, color .15s;
}
.selected-clear:hover { background: #fee2e2; color: #dc2626; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Catat Siswa Terlambat</h1>
        <p class="page-subtitle">Otomatis terhubung dengan jam digital real-time & jadwal pelajaran KBM</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('piket.siswa-terlambat.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

{{-- Banner Jam Digital & Status Jam KBM --}}
@php
    $slotStatus = $currentSlot['status'] ?? 'jam_pulang';
    $bannerGradient = match($slotStatus) {
        'kbm' => 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)',
        'istirahat' => 'linear-gradient(135deg, #d97706 0%, #78350f 100%)',
        default => 'linear-gradient(135deg, #334155 0%, #0f172a 100%)',
    };
    $autoJamKe = $currentSlot['jam_ke'] ?? 1;
    $autoWaktuMulai = $currentSlot['waktu_mulai'] ?? '07:00';
@endphp

<div class="card mb-24" id="topBannerCard" style="background: {{ $bannerGradient }}; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
    <div class="card-body" style="padding: 20px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 700;">
                    🕒 Waktu Real-Time Laptop / Perangkat
                </div>
                <div style="font-size: 32px; font-weight: 800; letter-spacing: 0.5px; font-family: monospace; margin-top: 2px;" id="liveClockDisplay">
                    {{ $now->format('H:i:s') }} WIB
                </div>
                <div style="font-size: 13px; color: #e2e8f0; margin-top: 2px;">
                    Hari {{ $hariIni }}, {{ $now->locale('id')->isoFormat('D MMMM YYYY') }}
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 20px; border-radius: 12px; min-width: 300px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #bfdbfe; font-weight: 700; letter-spacing: 0.5px;">
                    📌 Jam KBM Otomatis Terkoneksi
                </div>
                <div style="font-size: 17px; font-weight: 800; margin-top: 4px; color: #ffffff;" id="liveStatusDisplayBanner">
                    @if($slotStatus === 'kbm')
                        Jam Ke-{{ $currentSlot['jam_ke'] }} ({{ $currentSlot['waktu_label'] }})
                    @elseif($slotStatus === 'istirahat')
                        ☕ {{ $currentSlot['keterangan'] }}
                    @elseif($slotStatus === 'jam_pulang')
                        🏠 Jam Pulang Sekolah
                    @else
                        Jam Ke-1 (07:00 WIB)
                    @endif
                </div>
                <div style="font-size: 12px; margin-top: 4px; color: #86efac; font-weight: 600;">
                    ⚡ Tanpa Perlu Memilih Jam Manual
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul style="margin:0; padding-left:16px;">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Pencatatan Keterlambatan Siswa</h3>
        <span style="font-size:13px; color:var(--text-secondary);">
            📅 {{ \Carbon\Carbon::parse($todayDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </span>
    </div>
    <div class="card-body">
        <form action="{{ route('piket.siswa-terlambat.store') }}" method="POST" id="formTerlambat">
            @csrf
            <input type="hidden" name="id_siswa" id="hiddenIdSiswa" value="{{ old('id_siswa') }}">
            <input type="hidden" name="jam_ke_dipilih" id="hiddenJamKe" value="{{ old('jam_ke_dipilih', $autoJamKe) }}">
            <input type="hidden" name="jam_kedatangan" id="hiddenJamKedatangan" value="{{ old('jam_kedatangan', $autoWaktuMulai) }}">

            <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

                {{-- Cari Siswa --}}
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Cari / Pilih Siswa Terlambat <span style="color:red">*</span></label>
                    <div class="search-wrapper" id="searchWrapper">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchSiswa" class="search-input"
                               placeholder="Ketik nama atau NISN siswa..."
                               autocomplete="off"
                               value="">
                        <div class="search-dropdown" id="searchDropdown"></div>
                    </div>
                    {{-- Kartu siswa terpilih --}}
                    <div id="selectedCard" style="display:none;" class="selected-card">
                        <div class="selected-avatar" id="selectedAvatar">?</div>
                        <div class="selected-info">
                            <div class="sname" id="selectedName">-</div>
                            <div class="smeta" id="selectedMeta">-</div>
                        </div>
                        <button type="button" class="selected-clear" id="btnClear" title="Ganti siswa">✕</button>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="form-group">
                    <label class="form-label">Tanggal Keterlambatan <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', $todayDate) }}" required
                           id="inputTanggal">
                </div>

                {{-- Status Jam Kedatangan Display --}}
                <div class="form-group" id="jamInfoBox" style="display:flex; align-items:center; padding:10px 16px; background:var(--bg-page); border:1px solid var(--border); border-radius:8px; gap:12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:var(--primary);flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <div>
                        <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Status Jam Pelajaran Terkoneksi</div>
                        <div id="jamKedatanganDisplay" style="font-weight:800; color:var(--primary); font-size:15px;">
                            @if($slotStatus === 'kbm')
                                Jam Ke-{{ $currentSlot['jam_ke'] }} ({{ $currentSlot['waktu_label'] }})
                            @elseif($slotStatus === 'istirahat')
                                ☕ {{ $currentSlot['keterangan'] }}
                            @else
                                Jam Ke-1 (07:00 WIB)
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alasan --}}
            <div class="form-group" style="margin-top:16px;">
                <label class="form-label">Alasan Keterlambatan <span style="color:red">*</span></label>
                <textarea name="alasan" class="form-control" rows="3" required
                          placeholder="Tuliskan alasan siswa terlambat...">{{ old('alasan') }}</textarea>
            </div>

            {{-- Tindakan Piket --}}
            <div class="form-group">
                <label class="form-label">Tindakan Piket</label>
                <textarea name="tindakan_piket" class="form-control" rows="2"
                          placeholder="Diberikan surat izin masuk kelas (default jika kosong)">{{ old('tindakan_piket') }}</textarea>
                <div class="form-hint">Kosongkan untuk menggunakan tindakan default: "Diberikan surat izin masuk kelas"</div>
            </div>

            <div style="background:var(--bg-page); border:1px solid var(--border); border-radius:8px; padding:14px 18px; margin-bottom:20px;">
                <div style="font-weight:600; margin-bottom:6px; color:var(--text-primary);">ℹ️ Informasi Otomatis Sistem</div>
                <ul style="margin:0; padding-left:18px; color:var(--text-secondary); font-size:13px; line-height:1.9;">
                    <li>Jam kedatangan & jam KBM otomatis terdeteksi dari jam digital perangkat</li>
                    <li>Status absensi siswa di jurnal KBM akan otomatis diubah menjadi <strong>Terlambat</strong></li>
                    <li>Notifikasi otomatis dikirim ke <strong>guru yang mengajar</strong> & <strong>wali kelas</strong></li>
                    <li>Surat izin masuk kelas dapat dicetak setelah data disimpan</li>
                </ul>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn btn-primary" id="btnSubmit">💾 Simpan & Buat Surat Izin</button>
                <a href="{{ route('piket.siswa-terlambat.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@php
    $siswaDaftarData = $siswaList->map(function($s) {
        return [
            'id'    => $s->id_siswa,
            'nama'  => $s->nama,
            'nisn'  => $s->NISN,
            'kelas' => $s->kelas->nama_kelas ?? '-',
        ];
    })->values();
@endphp

@push('scripts')
<script>
// ── Live Digital Clock & KBM / Istirahat Schedule Solver ──────────
const seninKamisSlots = [
    { jam_ke: 1,  start: '07:00', end: '07:40' },
    { jam_ke: 2,  start: '07:40', end: '08:20' },
    { jam_ke: 3,  start: '08:20', end: '09:00' },
    { jam_ke: 4,  start: '09:00', end: '09:40' },
    { type: 'istirahat', name: 'Istirahat Pertama', start: '09:40', end: '10:00', jam_ke: 4, next_jam: 5 },
    { jam_ke: 5,  start: '10:00', end: '10:40' },
    { jam_ke: 6,  start: '10:40', end: '11:20' },
    { jam_ke: 7,  start: '11:20', end: '12:00' },
    { type: 'istirahat', name: 'Istirahat Kedua', start: '12:00', end: '13:00', jam_ke: 7, next_jam: 8 },
    { jam_ke: 8,  start: '13:00', end: '13:40' },
    { jam_ke: 9,  start: '13:40', end: '14:20' },
    { jam_ke: 10, start: '14:20', end: '15:00' },
];

const jumatSlots = [
    { jam_ke: 1,  start: '07:00', end: '07:30' },
    { jam_ke: 2,  start: '07:30', end: '08:00' },
    { jam_ke: 3,  start: '08:00', end: '08:30' },
    { jam_ke: 4,  start: '08:30', end: '09:00' },
    { jam_ke: 5,  start: '09:00', end: '09:30' },
    { type: 'istirahat', name: 'Istirahat Pertama', start: '09:30', end: '09:50', jam_ke: 5, next_jam: 6 },
    { jam_ke: 6,  start: '09:50', end: '10:20' },
    { jam_ke: 7,  start: '10:20', end: '10:50' },
    { jam_ke: 8,  start: '10:50', end: '11:20' },
    { type: 'istirahat', name: 'Istirahat Kedua', start: '11:20', end: '13:00', jam_ke: 8, next_jam: 9 },
    { jam_ke: 9,  start: '13:00', end: '13:30' },
    { jam_ke: 10, start: '13:30', end: '14:00' },
    { jam_ke: 11, start: '14:00', end: '14:30' },
    { jam_ke: 12, start: '14:30', end: '15:00' },
    { jam_ke: 13, start: '15:00', end: '15:30' },
];

function getSlotInfo(now) {
    const day = now.getDay(); // 0: Sunday, 1: Monday, ... 5: Friday, 6: Saturday
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const timeStr = `${h}:${m}`;

    if (day === 0 || day === 6) {
        return {
            status: 'libur',
            label: 'Hari Libur Sekolah',
            jam_ke: 1,
            waktu_mulai: '07:00',
            gradient: 'linear-gradient(135deg, #334155 0%, #0f172a 100%)'
        };
    }

    const slots = (day === 5) ? jumatSlots : seninKamisSlots;

    for (let slot of slots) {
        if (timeStr >= slot.start && timeStr < slot.end) {
            if (slot.type === 'istirahat') {
                return {
                    status: 'istirahat',
                    label: `☕ Waktu ${slot.name} (${slot.start} - ${slot.end} WIB)`,
                    jam_ke: slot.jam_ke,
                    waktu_mulai: slot.start,
                    gradient: 'linear-gradient(135deg, #d97706 0%, #78350f 100%)'
                };
            } else {
                return {
                    status: 'kbm',
                    label: `Jam Ke-${slot.jam_ke} (${slot.start} - ${slot.end} WIB)`,
                    jam_ke: slot.jam_ke,
                    waktu_mulai: slot.start,
                    gradient: 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)'
                };
            }
        }
    }

    if (timeStr < '07:00') {
        return {
            status: 'sebelum_kbm',
            label: 'Belum Masuk KBM (Pukul 07:00 WIB)',
            jam_ke: 1,
            waktu_mulai: '07:00',
            gradient: 'linear-gradient(135deg, #334155 0%, #0f172a 100%)'
        };
    }

    return {
        status: 'jam_pulang',
        label: '🏠 Jam Pulang Sekolah',
        jam_ke: (day === 5 ? 12 : 10),
        waktu_mulai: timeStr,
        gradient: 'linear-gradient(135deg, #334155 0%, #0f172a 100%)'
    };
}

function updateLiveClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');

    // Update Clock Display
    const clockEl = document.getElementById('liveClockDisplay');
    if (clockEl) clockEl.textContent = `${h}:${m}:${s} WIB`;

    // Solve Slot Info dynamically from browser time
    const slot = getSlotInfo(now);

    const bannerStatusEl = document.getElementById('liveStatusDisplayBanner');
    if (bannerStatusEl) bannerStatusEl.textContent = slot.label;

    const jamCardDisplay = document.getElementById('jamKedatanganDisplay');
    if (jamCardDisplay) {
        jamCardDisplay.textContent = slot.label;
        if (slot.status === 'istirahat') {
            jamCardDisplay.style.color = '#d97706';
        } else {
            jamCardDisplay.style.color = 'var(--primary)';
        }
    }

    const topBannerCard = document.getElementById('topBannerCard');
    if (topBannerCard) topBannerCard.style.background = slot.gradient;

    // Update Hidden Form Inputs
    const hiddenJamKe = document.getElementById('hiddenJamKe');
    const hiddenJamKedatangan = document.getElementById('hiddenJamKedatangan');
    if (hiddenJamKe) hiddenJamKe.value = slot.jam_ke;
    if (hiddenJamKedatangan) hiddenJamKedatangan.value = slot.waktu_mulai;
}
setInterval(updateLiveClock, 1000);
updateLiveClock();

// ── Data Siswa ──────────────────────────────────────
const siswaDaftar = @json($siswaDaftarData);

// ── Restore old selection ───────────────────────────
@if(old('id_siswa'))
(function(){
    const oldId = {{ old('id_siswa') }};
    const s = siswaDaftar.find(x => x.id == oldId);
    if (s) piliSiswa(s);
})();
@endif

// ── Search Logic ────────────────────────────────────
const searchEl    = document.getElementById('searchSiswa');
const dropdown    = document.getElementById('searchDropdown');
const selectedCard = document.getElementById('selectedCard');
let focusIdx = -1;

searchEl.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    if (!q) { dropdown.classList.remove('open'); return; }

    const matches = siswaDaftar.filter(s =>
        s.nama.toLowerCase().includes(q) || (s.nisn && s.nisn.toLowerCase().includes(q))
    ).slice(0, 15);

    if (matches.length === 0) {
        dropdown.innerHTML = '<div class="dropdown-empty">Tidak ada siswa ditemukan</div>';
    } else {
        dropdown.innerHTML = matches.map((s, i) => `
            <div class="dropdown-item" data-idx="${i}" onclick='piliSiswa(${JSON.stringify(s)})'>
                <span class="item-name">${s.nama}</span>
                <span class="item-meta">NISN: ${s.nisn || '-'} &nbsp;|&nbsp; Kelas: ${s.kelas}</span>
            </div>
        `).join('');
    }
    focusIdx = -1;
    dropdown.classList.add('open');
});

// Keyboard nav
searchEl.addEventListener('keydown', function(e) {
    const items = dropdown.querySelectorAll('.dropdown-item');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        focusIdx = Math.min(focusIdx + 1, items.length - 1);
        highlightItem(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        focusIdx = Math.max(focusIdx - 1, 0);
        highlightItem(items);
    } else if (e.key === 'Enter' && focusIdx >= 0) {
        e.preventDefault();
        items[focusIdx].click();
    } else if (e.key === 'Escape') {
        dropdown.classList.remove('open');
    }
});

function highlightItem(items) {
    items.forEach((el, i) => el.classList.toggle('focused', i === focusIdx));
    if (focusIdx >= 0) items[focusIdx].scrollIntoView({ block: 'nearest' });
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('searchWrapper').contains(e.target)) {
        dropdown.classList.remove('open');
    }
});

function piliSiswa(s) {
    document.getElementById('hiddenIdSiswa').value = s.id;
    searchEl.style.display = 'none';
    dropdown.classList.remove('open');

    document.getElementById('selectedAvatar').textContent = s.nama.charAt(0).toUpperCase();
    document.getElementById('selectedName').textContent = s.nama;
    document.getElementById('selectedMeta').textContent = `NISN: ${s.nisn || '-'} | Kelas: ${s.kelas}`;
    selectedCard.style.display = 'flex';
}

document.getElementById('btnClear').addEventListener('click', function() {
    document.getElementById('hiddenIdSiswa').value = '';
    searchEl.style.display = '';
    searchEl.value = '';
    searchEl.focus();
    selectedCard.style.display = 'none';
});

// ── Form Validation ─────────────────────────────────
document.getElementById('formTerlambat').addEventListener('submit', function(e) {
    if (!document.getElementById('hiddenIdSiswa').value) {
        e.preventDefault();
        alert('Silakan pilih siswa terlebih dahulu!');
        searchEl.focus();
        return;
    }
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').textContent = '⏳ Menyimpan...';
});
</script>
@endpush
@endsection
