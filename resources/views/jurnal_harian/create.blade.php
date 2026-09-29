@extends('layouts.app')

@section('title', 'Isi Jurnal Mengajar — Jurnal Sekolah')
@section('page-title', 'Jurnal Mengajar Saya')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Formulir Jurnal Harian KBM</h1>
        <p class="page-subtitle">Terkoneksi langsung dengan jam sekolah &amp; jadwal mengajar aktif saat ini</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali ke Jurnal</a>
    </div>
</div>

{{-- Banner Jam Digital & Status Jam KBM --}}
@php
    $accentBorder = match($slotStatus) {
        'kbm' => '#16a34a',
        'istirahat' => '#d97706',
        'jam_pulang' => '#64748b',
        'sebelum_kbm' => '#0284c7',
        'libur' => '#9333ea',
        default => '#1e3a8a'
    };
    $statusBadgeClass = match($slotStatus) {
        'kbm' => 'pill-kbm',
        'istirahat' => 'pill-istirahat',
        'jam_pulang' => 'pill-pulang',
        'sebelum_kbm' => 'pill-menunggu',
        'libur' => 'pill-libur',
        default => 'pill-kbm'
    };
@endphp

<div class="card mb-24 kbm-clock-card" style="border-left-color: {{ $accentBorder }};">
    <div class="card-body" style="padding: 18px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px;">
            {{-- Sisi Kiri: Jam & Tanggal --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="kbm-clock-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="clock-live-tag">
                        <span class="clock-live-dot"></span>
                        Waktu Real-Time Sekolah
                    </div>
                    <div class="clock-time-display">
                        <span class="clock-digits" id="liveClockDisplay">{{ $now->format('H:i:s') }}</span>
                        <span class="clock-tz-badge">WIB</span>
                    </div>
                    <div class="clock-date-display">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.8;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span id="liveDateDisplay">Hari {{ $currentDayIndo }}, {{ $now->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Status KBM --}}
            <div class="kbm-status-box">
                <div class="kbm-status-header">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Status Jam KBM Saat Ini
                </div>
                <div class="kbm-status-title">
                    @if($slotStatus === 'kbm')
                        Jam Ke-{{ $currentSlot['jam_ke'] }} ({{ $currentSlot['waktu_label'] }})
                    @elseif($slotStatus === 'istirahat')
                        {{ $currentSlot['keterangan'] }}
                    @elseif($slotStatus === 'jam_pulang')
                        Jam Pulang Sekolah
                    @elseif($slotStatus === 'sebelum_kbm')
                        Belum Masuk Jam KBM ({{ \App\Services\KbmService::getJamMasuk() }} WIB)
                    @elseif($slotStatus === 'libur')
                        Hari Libur Sekolah
                    @else
                        {{ $currentSlot['keterangan'] ?? 'Di Luar Jam KBM' }}
                    @endif
                </div>
                <div class="kbm-detail-badge">
                    <span class="kbm-status-pill {{ $statusBadgeClass }}">
                        <span class="kbm-pill-dot"></span>
                        <span>
                            @if($jadwalSelected)
                                Terkoneksi: Kelas {{ $jadwalSelected->kelas->nama_kelas ?? '-' }} — {{ $jadwalSelected->mapel }}
                            @elseif($slotStatus === 'kbm')
                                Tidak ada jadwal mengajar Anda di jam ini
                            @elseif($slotStatus === 'istirahat')
                                Sedang Waktu Istirahat — Tidak Ada KBM
                            @elseif($slotStatus === 'jam_pulang')
                                Jam Pulang Sekolah — KBM Telah Selesai
                            @elseif($slotStatus === 'sebelum_kbm')
                                KBM Dimulai Pukul {{ \App\Services\KbmService::getJamMasuk() }} WIB
                            @elseif($slotStatus === 'libur')
                                Tidak Ada Jadwal Mengajar
                            @else
                                Otomatis Terdeteksi Sistem
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JIKA SEDANG JAM MENGAJAR DAN JADWAL DITEMUKAN --}}
@if($jadwalSelected)
<div class="card mb-24" style="max-width: 800px; border-left: 4px solid var(--navy-primary);">
    <div class="card-header">
        <h3 class="card-title" style="color:var(--navy-primary); font-size:15px;">
            Informasi Jadwal Mengajar Aktif Saat Ini
        </h3>
    </div>
    <div class="card-body">
        <div class="grid-3" style="gap:16px;">
            <div>
                <div class="text-muted" style="font-size:12px;">Kelas Mengajar</div>
                <div style="font-size:16px; font-weight:700; color:var(--navy-primary); margin-top:4px;">
                    <span class="badge badge-navy" style="font-size:14px; padding:6px 14px;">{{ $jadwalSelected->kelas->nama_kelas ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Mata Pelajaran</div>
                <div style="font-size:15px; font-weight:700; margin-top:4px;">{{ $jadwalSelected->mapel }}</div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Waktu &amp; Ruangan</div>
                <div style="font-size:14px; font-weight:700; margin-top:4px;">
                    Jam Ke-{{ $jadwalSelected->jam_ke }} ({{ \App\Services\KbmService::getLabelWaktu($jadwalSelected->hari, $jadwalSelected->jam_ke) }})
                    @if($jadwalSelected->ruang)
                        <div style="font-size:12px; color:#64748b; font-weight:normal; margin-top:2px;">Ruang: {{ $jadwalSelected->ruang }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Catatan Jurnal Mengajar</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurnal-harian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jadwal" value="{{ $jadwalSelected->id_jadwal }}">

            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Jurnal <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" style="max-width:220px;" required readonly>
                <small class="text-muted" style="display:block; margin-top:4px;">Terkoneksi otomatis dengan tanggal hari ini.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="materi">Materi Pelajaran Utama <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi') }}" class="form-control" placeholder="Contoh: Bab 3 Persamaan Kuadrat" required autofocus>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="sub_materi">Sub Materi / Pokok Bahasan</label>
                <input type="text" id="sub_materi" name="sub_materi" value="{{ old('sub_materi') }}" class="form-control" placeholder="Contoh: Struktur Kontrol Percabangan If-Else">
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="catatan_pengajaran">Catatan Pengajaran &amp; Evaluasi Kelas</label>
                <textarea id="catatan_pengajaran" name="catatan_pengajaran" class="form-control" rows="3" placeholder="Catatan respon siswa, keaktifan, kendala KBM, atau penugasan">{{ old('catatan_pengajaran') }}</textarea>
            </div>

            <div class="d-flex gap-12 align-center flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg" style="font-weight:700; padding:10px 24px;">
                    SIMPAN JURNAL MENGAJAR
                </button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- JIKA BUKAN JAM MENGAJAR / ISTIRAHAT / PULANG / LIBUR / TIDAK ADA JADWAL --}}
@else
<div class="card" style="max-width: 800px;">
    <div class="card-body" style="padding: 36px 24px; text-align: center;">
        @if($slotStatus === 'istirahat')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="2" x2="6" y2="4"></line><line x1="10" y1="2" x2="10" y2="4"></line><line x1="14" y1="2" x2="14" y2="4"></line></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #b45309; margin-bottom: 8px;">
                Saat Ini Sedang Waktu Istirahat
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto; line-height: 1.6;">
                Waktu {{ $currentSlot['keterangan'] }}. Kegiatan belajar mengajar sedang dijeda. Pengisian formulir jurnal akan aktif otomatis saat jam pelajaran Anda dimulai.
            </p>
        @elseif($slotStatus === 'jam_pulang')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Jam Pulang Sekolah — KBM Telah Selesai
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto 20px auto; line-height: 1.6;">
                Kegiatan Belajar Mengajar (KBM) hari ini telah selesai ({{ $currentSlot['keterangan'] ?? 'Jam Pulang' }}). Pengisian formulir jurnal ditutup karena sudah melewati jam kepulangan sekolah.
            </p>
        @elseif($slotStatus === 'sebelum_kbm')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #0284c7; margin-bottom: 8px;">
                Belum Masuk Jam KBM Sekolah
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto; line-height: 1.6;">
                Jam kegiatan belajar mengajar sekolah dimulai pukul <strong>{{ \App\Services\KbmService::getJamMasuk() }} WIB</strong>. Sistem akan otomatis mendeteksi jadwal mengajar Anda saat jam masuk tiba.
            </p>
        @elseif($slotStatus === 'libur')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#7e22ce" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #7e22ce; margin-bottom: 8px;">
                Hari Libur Sekolah
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto; line-height: 1.6;">
                Hari {{ $currentDayIndo }} adalah hari libur sekolah. Tidak ada kegiatan belajar mengajar (KBM).
            </p>
        @else
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px;">
                Tidak Ada Jam Mengajar di Jam Ke-{{ $currentSlot['jam_ke'] ?? '-' }} Saat Ini
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 520px; margin: 0 auto 20px auto; line-height: 1.6;">
                Anda tidak memiliki jadwal mengajar pada jam ini ({{ $currentSlot['waktu_label'] ?? '' }}). Pengisian formulir jurnal hanya dapat dilakukan saat jam mengajar Anda aktif.
            </p>
        @endif

        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">
            &larr; Lihat Riwayat Jurnal Mengajar Saya
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const clockEl = document.getElementById('liveClockDisplay');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateClock, 1000);
});
</script>
@endpush
