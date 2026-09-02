@extends('layouts.app')

@section('title', 'Isi Jurnal Harian — Jurnal Sekolah')
@section('page-title', 'Isi Jurnal Harian')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Formulir Jurnal Harian KBM</h1>
        <p class="page-subtitle">Pendeteksian otomatis jam KBM &amp; jadwal mengajar berdasarkan waktu perangkat</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

{{-- Banner Jam Digital & Pendeteksian Otomatis --}}
<div class="card mb-24" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff;">
    <div class="card-body" style="padding: 20px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 600;">
                    🕒 Waktu Perangkat / Laptop Saat Ini
                </div>
                <div style="font-size: 28px; font-weight: 800; letter-spacing: 0.5px; font-family: monospace; margin-top: 2px;" id="liveClockDisplay">
                    {{ $now->format('H:i:s') }} WIB
                </div>
                <div style="font-size: 13px; color: #cbd5e1; margin-top: 2px;">
                    Hari {{ $currentDayIndo }}, {{ $now->translatedFormat('d F Y') }}
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 18px; border-radius: 10px; min-width: 260px;">
                <div style="font-size: 11.5px; text-transform: uppercase; color: #93c5fd; font-weight: 700; letter-spacing: 0.5px;">
                    📌 Status Jam KBM Terdeteksi
                </div>
                <div style="font-size: 16px; font-weight: 700; margin-top: 4px;">
                    @if(isset($currentSlot['jam_ke']))
                        Jam Ke-{{ $currentSlot['jam_ke'] }} ({{ $currentSlot['waktu_label'] }})
                    @else
                        {{ $currentSlot['keterangan'] ?? 'Di luar jam KBM' }} ({{ $currentSlot['waktu_label'] }})
                    @endif
                </div>
                @if($jadwalSelected)
                    <div style="font-size: 12px; color: #60a5fa; margin-top: 4px; font-weight: 600;">
                        ✅ Terkoneksi: Kelas {{ $jadwalSelected->kelas->nama_kelas ?? '-' }} — {{ $jadwalSelected->mapel }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Pilihan Manual / Ganti Jadwal Mengajar jika Diperlukan --}}
<div class="card mb-24" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('jurnal-harian.create') }}" method="GET" class="d-flex align-center gap-12 flex-wrap" id="form-pilih-jadwal">
            <label for="id_jadwal" class="form-label" style="margin:0; white-space:nowrap; font-weight:600;">Jadwal Mengajar Terhubung:</label>
            <select id="id_jadwal" name="id_jadwal" onchange="this.form.submit()" class="form-control select-search" style="min-width:400px; flex-grow:1;">
                <option value="">-- Pilih / Ganti Jadwal KBM --</option>
                @foreach($jadwalList as $j)
                <option value="{{ $j->id_jadwal }}" {{ ($jadwalSelected && $jadwalSelected->id_jadwal == $j->id_jadwal) ? 'selected' : '' }}>
                    {{ $j->hari }} | Jam Ke-{{ $j->jam_ke }} ({{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) }}) | Kelas {{ $j->kelas->nama_kelas ?? '-' }} | {{ $j->mapel }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($jadwalSelected)
<div class="card mb-24" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">Informasi Jadwal KBM Terpilih</h3>
    </div>
    <div class="card-body">
        <div class="grid-3" style="gap:16px;">
            <div>
                <div class="text-muted" style="font-size:12px;">Kelas</div>
                <div style="font-size:16px; font-weight:700; color:var(--navy-primary);">
                    <span class="badge badge-navy" style="font-size:14px; padding:6px 12px;">{{ $jadwalSelected->kelas->nama_kelas ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Mata Pelajaran</div>
                <div style="font-size:15px; font-weight:700;">{{ $jadwalSelected->mapel }}</div>
            </div>
            <div>
                <div class="text-muted" style="font-size:12px;">Waktu &amp; Jam Ke</div>
                <div style="font-size:14px; font-weight:700;">
                    Jam Ke-{{ $jadwalSelected->jam_ke }} ({{ \App\Services\KbmService::getLabelWaktu($jadwalSelected->hari, $jadwalSelected->jam_ke) }})
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Catatan Jurnal &amp; Kehadiran KBM</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jurnal-harian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_jadwal" value="{{ $jadwalSelected->id_jadwal }}">
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">

            <div class="form-group mb-16">
                <label class="form-label" for="materi">Materi Pelajaran Utama <span class="req">*</span></label>
                <input type="text" id="materi" name="materi" value="{{ old('materi') }}" class="form-control" placeholder="Contoh: Bab 3 Pengenalan Dasar Algoritma &amp; Pemrograman" required>
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
                    <option value="terlaksana" {{ old('status_keterlaksanaan') == 'terlaksana' ? 'selected' : '' }}>✅ Terlaksana (Hadir Mengajar)</option>
                    <option value="izin_guru" {{ old('status_keterlaksanaan') == 'izin_guru' ? 'selected' : '' }}>ℹ️ Izin Guru (Ada Keperluan / Penugasan)</option>
                    <option value="sakit_guru" {{ old('status_keterlaksanaan') == 'sakit_guru' ? 'selected' : '' }}>🏥 Sakit Guru</option>
                    <option value="dispen_guru" {{ old('status_keterlaksanaan') == 'dispen_guru' ? 'selected' : '' }}>📋 Dispensasi Guru / Tugas Luar</option>
                    <option value="pengganti" {{ old('status_keterlaksanaan') == 'pengganti' ? 'selected' : '' }}>🔄 Digantikan Guru Piket / Pengganti</option>
                    <option value="tidak_terlaksana" {{ old('status_keterlaksanaan') == 'tidak_terlaksana' ? 'selected' : '' }}>❌ Tidak Terlaksana</option>
                </select>
            </div>

            <div class="d-flex gap-12 align-center flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg" style="font-weight:700;">
                    💾 SIMPAN JURNAL &amp; LANJUT ABSENSI SISWA
                </button>
                <a href="{{ route('jurnal-harian.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@else
<div class="alert alert-info" style="max-width: 800px;">
    <div>
        <strong>ℹ️ Informasi Jadwal:</strong> Tidak ditemukan jadwal mengajar otomatis pada jam ini. Silakan pilih jadwal mengajar KBM dari dropdown di atas untuk mengisi jurnal.
    </div>
</div>
@endif

<script>
    // Live clock script untuk jam laptop/perangkat
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClockDisplay');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
</script>
@endsection