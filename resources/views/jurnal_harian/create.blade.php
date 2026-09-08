@extends('layouts.app')

@section('title', 'Isi Jurnal Mengajar — Jurnal Sekolah')
@section('page-title', 'Jurnal Mengajar Saya')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Formulir Jurnal Harian KBM</h1>
        <p class="page-subtitle">Otomatis terkoneksi dengan jam laptop/perangkat &amp; jadwal mengajar saat ini</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali ke Jurnal</a>
    </div>
</div>

<!-- BANNER SINKRONISASI JAM LAPTOP AUTOMATIS -->
<div class="alert alert-info mb-16 d-flex align-center gap-12" id="laptop-time-banner">
    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px; height:22px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    <div>
        <strong>Waktu & Hari Laptop Anda:</strong> 
        <span id="laptop-time-display" class="fw-bold text-navy">Memuat jam laptop...</span>
        <span class="badge badge-success" style="margin-left:8px;">Otomatis Terbaca</span>
    </div>
</div>

<div class="card mb-24">
    <div class="card-body">
        <form id="form-pilih-jadwal" action="{{ route('jurnal-harian.create') }}" method="GET" class="d-flex align-center gap-12 flex-wrap">
            <label for="id_jadwal" class="form-label" style="margin:0; white-space:nowrap;">Pilih Jadwal Mengajar:</label>
            <select id="id_jadwal" name="id_jadwal" onchange="this.form.submit()" class="form-control select-search" style="min-width:450px;" placeholder="Ketik / Cari Jadwal Mengajar KBM...">
                <option value="">-- Pilih Jadwal KBM --</option>
                @foreach($jadwalList as $j)
                <option value="{{ $j->id_jadwal }}" 
                        data-hari="{{ $j->hari }}" 
                        data-jam-ke="{{ $j->jam_ke }}"
                        data-waktu-mulai="{{ $j->waktu_mulai }}"
                        data-waktu-selesai="{{ $j->waktu_selesai }}"
                        {{ ($jadwalSelected && $jadwalSelected->id_jadwal == $j->id_jadwal) ? 'selected' : '' }}>
                    {{ $j->hari }} | Jam {{ $j->jam_ke }} ({{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) ?: ($j->waktu_mulai . ' - ' . $j->waktu_selesai) }}) | Kelas {{ $j->kelas->nama_kelas ?? '-' }} | {{ $j->mapel }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Banner Jam Digital & Status Jam KBM --}}
<div class="card mb-24" style="background: {{ $bannerColor }}; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
    <div class="card-body" style="padding: 22px 26px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 700;">
                    🕒 Waktu Laptop / Perangkat Saat Ini
                </div>
                <div style="font-size: 32px; font-weight: 800; letter-spacing: 0.5px; font-family: monospace; margin-top: 2px;" id="liveClockDisplay">
                    {{ $now->format('H:i:s') }} WIB
                </div>
                <div style="font-size: 13.5px; color: #e2e8f0; margin-top: 2px;">
                    Hari {{ $currentDayIndo }}, {{ $now->translatedFormat('d F Y') }}
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 20px; border-radius: 12px; min-width: 280px;">
                <div style="font-size: 11.5px; text-transform: uppercase; color: #bfdbfe; font-weight: 700; letter-spacing: 0.5px;">
                    📌 Status Jam KBM Saat Ini
                </div>
                <div style="font-size: 17px; font-weight: 700; margin-top: 4px;">
                    @if($slotStatus === 'kbm')
                        Jam Ke-{{ $currentSlot['jam_ke'] }} ({{ $currentSlot['waktu_label'] }})
                    @elseif($slotStatus === 'istirahat')
                        ☕ {{ $currentSlot['keterangan'] }}
                    @elseif($slotStatus === 'jam_pulang')
                        🏠 Jam Pulang Sekolah
                    @elseif($slotStatus === 'sebelum_kbm')
                        🌅 Belum Masuk Jam KBM (07:00)
                    @elseif($slotStatus === 'libur')
                        🏖️ Hari Libur Sekolah
                    @else
                        {{ $currentSlot['keterangan'] ?? 'Di Luar Jam KBM' }}
                    @endif
                </div>
                <div style="font-size: 12.5px; margin-top: 5px; font-weight: 600;">
                    @if($jadwalSelected)
                        <span style="color: #86efac;">✅ Terkoneksi: Kelas {{ $jadwalSelected->kelas->nama_kelas ?? '-' }} — {{ $jadwalSelected->mapel }}</span>
                    @elseif($slotStatus === 'kbm')
                        <span style="color: #fde047;">ℹ️ Tidak ada jadwal mengajar di jam ini</span>
                    @elseif($slotStatus === 'istirahat')
                        <span style="color: #fed7aa;">Sedang Waktu Istirahat — Tidak Ada KBM</span>
                    @elseif($slotStatus === 'jam_pulang')
                        <span style="color: #fca5a5;">Jam Pulang Sekolah, Tidak Ada KBM</span>
                    @elseif($slotStatus === 'sebelum_kbm')
                        <span style="color: #cbd5e1;">KBM Dimulai Pukul 07:00 WIB</span>
                    @elseif($slotStatus === 'libur')
                        <span style="color: #a7f3d0;">Tidak Ada Jadwal Mengajar</span>
                    @endif
                </div>
=======
                @if($jadwalSelected)
                    <div style="font-size: 12px; color: #60a5fa; margin-top: 4px; font-weight: 600;">
                         Terkoneksi: Kelas {{ $jadwalSelected->kelas->nama_kelas ?? '-' }} — {{ $jadwalSelected->mapel }}
                    </div>
                @endif
>>>>>>> 78e988b10ce50ec303f72c4288dd910aceb5a3b5
            </div>
        </div>
    </div>
</div>

{{-- JIKA SEDANG JAM MENGAJAR DAN JADWAL DITEMUKAN --}}
@if($jadwalSelected)
<div class="card mb-24" style="max-width: 800px; border-left: 4px solid var(--navy-primary);">
    <div class="card-header" style="background:#f8fafc;">
        <h3 class="card-title" style="color:var(--navy-primary); font-size:15px;">
            📚 Informasi Jadwal Mengajar Saat Ini
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

<<<<<<< HEAD
            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Jurnal (Otomatis Laptop) <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" style="max-width:220px;" required>
                <small class="text-muted" style="display:block; margin-top:4px;">Diisi otomatis dari sistem tanggal & jam laptop Anda.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="materi">Materi Pelajaran Utama <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi') }}" class="form-control" placeholder="Contoh: Bab 3 Persamaan Kuadrat" required>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="sub_materi">Sub Materi / Pokok Bahasan</label>
                <input type="text" id="sub_materi" name="sub_materi" value="{{ old('sub_materi') }}" class="form-control" placeholder="Contoh: Struktur Kontrol Percabangan If-Else">
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="catatan_pengajaran">Catatan Pengajaran &amp; Evaluasi Kelas</label>
                <textarea id="catatan_pengajaran" name="catatan_pengajaran" class="form-control" rows="3" placeholder="Catatan respon siswa, keaktifan, kendala KBM, atau penugasan">{{ old('catatan_pengajaran') }}</textarea>
            </div>

            <div class="form-group mb-24">
                <label class="form-label" for="status_keterlaksanaan">Status Keterlaksanaan &amp; Kehadiran Guru <span class="req">*</span></label>
                <select id="status_keterlaksanaan" name="status_keterlaksanaan" class="form-control" required>
                    <option value="terlaksana" {{ old('status_keterlaksanaan') == 'terlaksana' ? 'selected' : '' }}> Terlaksana (Hadir Mengajar)</option>
                    <option value="izin_guru" {{ old('status_keterlaksanaan') == 'izin_guru' ? 'selected' : '' }}> Izin Guru (Ada Keperluan / Penugasan)</option>
                    <option value="sakit_guru" {{ old('status_keterlaksanaan') == 'sakit_guru' ? 'selected' : '' }}> Sakit Guru</option>
                    <option value="dispen_guru" {{ old('status_keterlaksanaan') == 'dispen_guru' ? 'selected' : '' }}> Dispensasi Guru / Tugas Luar</option>
                    <option value="pengganti" {{ old('status_keterlaksanaan') == 'pengganti' ? 'selected' : '' }}> Digantikan Guru Piket / Pengganti</option>
                    <option value="tidak_terlaksana" {{ old('status_keterlaksanaan') == 'tidak_terlaksana' ? 'selected' : '' }}> Tidak Terlaksana</option>
                </select>
            </div>

            <div class="d-flex gap-12 align-center flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg" style="font-weight:700; padding:10px 24px;">
                    💾 SIMPAN JURNAL MENGAJAR
                </button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- JIKA BUKAN JAM MENGAJAR / ISTIRAHAT / PULANG / LIBUR --}}
@else
<div class="card" style="max-width: 800px;">
    <div class="card-body" style="padding: 32px 24px; text-align: center;">
        @if($slotStatus === 'istirahat')
            <div style="font-size: 48px; margin-bottom: 12px;">☕</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #b45309; margin-bottom: 8px;">
                Saat Ini Sedang Waktu Istirahat
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto;">
                Waktu {{ $currentSlot['keterangan'] }}. Kegiatan belajar mengajar sedang dijeda. Pengisian formulir jurnal akan aktif otomatis saat jam pelajaran berikutnya dimulai.
            </p>
        @elseif($slotStatus === 'jam_pulang')
            <div style="font-size: 48px; margin-bottom: 12px;">🏠</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Jam Pulang Sekolah, Tidak Ada KBM
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto 20px auto;">
                Kegiatan Belajar Mengajar (KBM) hari ini telah selesai (Jam pulang sekolah, tidak ada kbm). Anda tidak dapat mengisi formulir jurnal di luar jam KBM.
            </p>
        @elseif($slotStatus === 'sebelum_kbm')
            <div style="font-size: 48px; margin-bottom: 12px;">🌅</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Belum Masuk Jam KBM Sekolah
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto;">
                Jam kegiatan belajar mengajar sekolah dimulai pukul <strong>07:00 WIB</strong>. Sistem akan otomatis mendeteksi jadwal mengajar Anda saat jam masuk tiba.
            </p>
        @elseif($slotStatus === 'libur')
            <div style="font-size: 48px; margin-bottom: 12px;">🏖️</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #065f46; margin-bottom: 8px;">
                Hari Libur Sekolah
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px auto;">
                Hari {{ $currentDayIndo }} adalah hari libur sekolah. Tidak ada jadwal kegiatan belajar mengajar.
            </p>
        @else
            <div style="font-size: 48px; margin-bottom: 12px;">📖</div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e40af; margin-bottom: 8px;">
                Tidak Ada Jadwal Mengajar di Jam Ke-{{ $currentSlot['jam_ke'] ?? '-' }}
            </h3>
            <p style="color: #64748b; font-size: 14px; max-width: 500px; margin: 0 auto 20px auto;">
                Anda tidak memiliki jadwal mengajar pada jam ini ({{ $currentSlot['waktu_label'] ?? '' }}). Formulir jurnal mengajar akan muncul secara otomatis saat jam mengajar Anda tiba.
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
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function updateLaptopClock() {
        const now = new Date();
        const dayName = daysIndo[now.getDay()];
        const dateNum = String(now.getDate()).padStart(2, '0');
        const monthName = monthsIndo[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        // Update Banner Display
        const timeDisplay = document.getElementById('laptop-time-display');
        if (timeDisplay) {
            timeDisplay.textContent = `${dayName}, ${dateNum} ${monthName} ${year} — ${hours}:${minutes}:${seconds} WIB/WITA`;
        }

        // Auto-set Date Input (YYYY-MM-DD) if empty or on load
        const dateInput = document.getElementById('tanggal');
        if (dateInput && !dateInput.dataset.userEdited) {
            const formattedDate = `${year}-${String(now.getMonth() + 1).padStart(2, '0')}-${dateNum}`;
            dateInput.value = formattedDate;
        }

        return { dayName, hours, minutes, formattedDate: `${year}-${String(now.getMonth() + 1).padStart(2, '0')}-${dateNum}` };
    }

    const currentLaptopState = updateLaptopClock();
    setInterval(updateLaptopClock, 1000);

    const dateInput = document.getElementById('tanggal');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            this.dataset.userEdited = "true";
        });
    }

    // Auto Schedule Selection based on Laptop Day & Time if id_jadwal not explicitly passed in URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectJadwal = document.getElementById('id_jadwal');

    if (selectJadwal && !urlParams.has('id_jadwal') && selectJadwal.options.length > 1) {
        const currentDay = currentLaptopState.dayName;
        const currentTimeStr = `${currentLaptopState.hours}:${currentLaptopState.minutes}`;

        let matchedIndex = -1;
        let matchedByDay = -1;

        for (let i = 1; i < selectJadwal.options.length; i++) {
            const opt = selectJadwal.options[i];
            const optHari = opt.getAttribute('data-hari');
            const wMulai = opt.getAttribute('data-waktu-mulai');
            const wSelesai = opt.getAttribute('data-waktu-selesai');

            if (optHari === currentDay) {
                if (matchedByDay === -1) matchedByDay = i;
                if (wMulai && wSelesai && currentTimeStr >= wMulai && currentTimeStr <= wSelesai) {
                    matchedIndex = i;
                    break;
                }
            }
        }

        const targetIndex = matchedIndex !== -1 ? matchedIndex : matchedByDay;

        if (targetIndex !== -1 && selectJadwal.selectedIndex !== targetIndex) {
            selectJadwal.selectedIndex = targetIndex;
            // Submit form to load selected schedule details
            selectJadwal.form.submit();
        }
    }
});
</script>
@endpush
