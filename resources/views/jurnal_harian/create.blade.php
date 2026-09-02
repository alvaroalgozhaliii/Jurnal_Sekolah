@extends('layouts.app')

@section('title', 'Isi Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Isi Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Formulir Jurnal Harian KBM</h1>
        <p class="page-subtitle">Pilih jadwal mengajar dan lengkapi catatan KBM</p>
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

@if($jadwalSelected)
<div class="card mb-24" style="max-width: 750px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Jadwal Terpilih</h3>
    </div>
    <div class="card-body">
        <div class="grid-3">
            <div><span class="text-muted">Kelas:</span> <strong><span class="badge badge-navy">{{ $jadwalSelected->kelas->nama_kelas ?? '-' }}</span></strong></div>
            <div><span class="text-muted">Mata Pelajaran:</span> <strong>{{ $jadwalSelected->mapel }}</strong></div>
            <div><span class="text-muted">Jam Pembelajaran:</span> <strong>Jam {{ $jadwalSelected->jam_ke }} — {{ \App\Services\KbmService::getLabelWaktu($jadwalSelected->hari, $jadwalSelected->jam_ke) ?: ($jadwalSelected->waktu_mulai . ' - ' . $jadwalSelected->waktu_selesai) }}</strong></div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 750px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Catatan Jurnal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurnal-harian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jadwal" value="{{ $jadwalSelected->id_jadwal }}">

            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Jurnal (Otomatis Laptop) <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" style="max-width:220px;" required>
                <small class="text-muted" style="display:block; margin-top:4px;">Diisi otomatis dari sistem tanggal & jam laptop Anda.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="materi">Materi Pelajaran Utama <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi') }}" class="form-control" placeholder="Contoh: Bab 3 Persamaan Kuadrat" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="sub_materi">Sub Materi / Pokok Bahasan</label>
                <input type="text" id="sub_materi" name="sub_materi" value="{{ old('sub_materi') }}" class="form-control" placeholder="Contoh: Rumus ABC dan Diskriminan">
            </div>

            <div class="form-group">
                <label class="form-label" for="catatan_pengajaran">Catatan Pengajaran & Evaluasi Kelas</label>
                <textarea id="catatan_pengajaran" name="catatan_pengajaran" class="form-control" rows="4" placeholder="Catatan respon siswa, keaktifan, atau tugas KBM">{{ old('catatan_pengajaran') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="status_keterlaksanaan">Status Keterlaksanaan <span class="req">*</span></label>
                <select id="status_keterlaksanaan" name="status_keterlaksanaan" class="form-control" required>
                    <option value="terlaksana" {{ old('status_keterlaksanaan') == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                    <option value="tidak_terlaksana" {{ old('status_keterlaksanaan') == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                    <option value="kosong" {{ old('status_keterlaksanaan') == 'kosong' ? 'selected' : '' }}>Kosong</option>
                    <option value="pengganti" {{ old('status_keterlaksanaan') == 'pengganti' ? 'selected' : '' }}>Pengganti</option>
                </select>
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary btn-lg">SIMPAN JURNAL & LANJUT ABSENSI SISWA</button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@else
<div class="alert alert-info">
    <div>Silakan pilih jadwal mengajar terlebih dahulu dari dropdown di atas.</div>
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