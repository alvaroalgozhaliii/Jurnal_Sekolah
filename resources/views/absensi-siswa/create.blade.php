@extends('layouts.app')

@section('title', 'Input Absensi Siswa — Jurnal Sekolah')
@section('page-title', 'Input Absensi Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Input Absensi Siswa (Batch)</h1>
        <p class="page-subtitle">Terkoneksi langsung dengan jam sekolah &amp; jadwal mengajar aktif saat ini</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">&larr; Kembali ke Data Absensi</a>
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
                            @if($jurnalSelected && $jurnalSelected->jadwal)
                                Terkoneksi: Kelas {{ $jurnalSelected->jadwal->kelas->nama_kelas ?? '-' }} — {{ $jurnalSelected->mapel }}
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

{{-- JIKA BUKAN ROLE GURU (MISAL ADMIN/PIKET/WAKA), TETAP BISA PILIH JURNAL MANUAL --}}
@if($activeAccess !== 'guru' && !Auth::user()->isGuru())
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('absensi-siswa.create') }}" method="GET" class="d-flex align-center gap-12 flex-wrap">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Jurnal Harian:</label>
            <select name="id_jurnal" onchange="this.form.submit()" class="form-control select-search" style="max-width:500px;">
                <option value="">Pilih Jurnal KBM</option>
                @foreach($jurnalList as $j)
                <option value="{{ $j->id_jurnal }}" {{ ($jurnalSelected && $jurnalSelected->id_jurnal == $j->id_jurnal) ? 'selected' : '' }}>
                    {{ $j->tanggal }} | {{ $j->mapel }} | Kelas {{ $j->jadwal->kelas->nama_kelas ?? '-' }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

{{-- JIKA SEDANG JAM MENGAJAR DAN JURNAL/KELAS AKTIF DITEMUKAN --}}
@if($jurnalSelected && $siswaList->count() > 0)
<div class="card mb-24" style="border-left: 4px solid var(--navy-primary);">
    <div class="card-header" style="background:#f8fafc;">
        <h3 class="card-title" style="color:var(--navy-primary); font-size:15px;">
            Informasi Kelas Mengajar Aktif Saat Ini
        </h3>
    </div>
    <div class="card-body">
        <div class="grid-3" style="gap:16px;">
            <div>
                <div class="text-muted" style="font-size:12px;">Kelas Mengajar</div>
                <div style="font-size:16px; font-weight:700; color:var(--navy-primary); margin-top:4px;">
                    <span class="badge badge-navy" style="font-size:14px; padding:6px 14px;">{{ $jurnalSelected->jadwal->kelas->nama_kelas ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Mata Pelajaran</div>
                <div style="font-size:15px; font-weight:700; margin-top:4px;">{{ $jurnalSelected->mapel }}</div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Waktu &amp; Total Siswa</div>
                <div style="font-size:14px; font-weight:700; margin-top:4px;">
                    Jam Ke-{{ $jurnalSelected->jadwal->jam_ke ?? '-' }} ({{ \App\Services\KbmService::getLabelWaktu($jurnalSelected->jadwal->hari ?? 'Senin', $jurnalSelected->jadwal->jam_ke ?? 1) }})
                    <div style="font-size:12px; color:#10b981; font-weight:600; margin-top:2px;">{{ $siswaList->count() }} Siswa di Kelas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Presensi Siswa Kelas ({{ $siswaList->count() }} Siswa)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form action="{{ route('absensi-siswa.storeBatch') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jurnal" value="{{ $jurnalSelected->id_jurnal }}">
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="no-col">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Status Kehadiran *</th>
                            <th>Jam Masuk (jika Terlambat)</th>
                            <th>Selisih Terlambat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($siswaList as $s)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td class="text-muted fw-bold">{{ $s->nisn }}</td>
                        <td class="fw-bold text-navy">{{ $s->nama }}</td>
                        <td>
                            <select name="absensi[{{ $s->id_siswa }}]" class="form-control" required style="padding:4px 8px;">
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                        <td><input type="time" name="jam_masuk[{{ $s->id_siswa }}]" class="form-control" style="padding:4px 8px;"></td>
                        <td><input type="number" name="menit_terlambat[{{ $s->id_siswa }}]" min="0" placeholder="Menit" class="form-control" style="max-width:90px; padding:4px 8px;"></td>
                        <td><input type="text" name="keterangan[{{ $s->id_siswa }}]" placeholder="Keterangan opsional" class="form-control" style="padding:4px 8px;"></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer" style="padding: 16px 20px;">
                <button type="submit" class="btn btn-success btn-lg" style="font-weight:700; padding:10px 24px;">
                    SIMPAN ABSENSI SISWA
                </button>
                <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary btn-lg" style="margin-left:8px;">Batal</a>
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
                Waktu {{ $currentSlot['keterangan'] }}. Kegiatan belajar mengajar sedang dijeda. Input absensi siswa akan aktif otomatis saat jam pelajaran Anda dimulai.
            </p>
        @elseif($slotStatus === 'jam_pulang')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Jam Pulang Sekolah — KBM Telah Selesai
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto 20px auto; line-height: 1.6;">
                Kegiatan Belajar Mengajar (KBM) hari ini telah selesai ({{ $currentSlot['keterangan'] ?? 'Jam Pulang' }}). Input absensi siswa ditutup karena sudah melewati jam kepulangan sekolah.
            </p>
        @elseif($slotStatus === 'sebelum_kbm')
            <div style="margin-bottom: 14px;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></div>
            <h3 style="font-size: 20px; font-weight: 700; color: #0284c7; margin-bottom: 8px;">
                Belum Masuk Jam KBM Sekolah
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto; line-height: 1.6;">
                Jam kegiatan belajar mengajar sekolah dimulai pukul <strong>{{ \App\Services\KbmService::getJamMasuk() }} WIB</strong>. Sistem akan otomatis mendeteksi kelas mengajar Anda saat jam masuk tiba.
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
                Anda tidak memiliki jadwal mengajar pada jam ini ({{ $currentSlot['waktu_label'] ?? '' }}). Input absensi siswa hanya dapat dilakukan saat jam mengajar Anda aktif.
            </p>
        @endif

        <a href="{{ route('absensi-siswa.index') }}" class="btn btn-secondary">
            &larr; Lihat Rekapitulasi Absensi Siswa
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