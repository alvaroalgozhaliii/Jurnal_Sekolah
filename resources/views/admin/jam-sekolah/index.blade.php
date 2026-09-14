@extends('layouts.app')

@section('title', 'Pengaturan Jam Sekolah — Jurnal Sekolah')
@section('page-title', 'Pengaturan Jam Sekolah')

@section('content')
<style>
.jam-card-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    color: #ffffff;
    border-radius: var(--radius-lg, 12px);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(37,99,235,0.25);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.jam-grid-settings {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}
.jam-section-box {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg, 12px);
    padding: 20px;
    box-shadow: var(--shadow-sm);
}
.jam-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}
.slot-table-wrapper {
    overflow-x: auto;
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-top: 12px;
}
.slot-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.slot-table th, .slot-table td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    text-align: left;
}
.slot-table th {
    background: var(--bg-card-header);
    color: var(--text-secondary);
    font-weight: 600;
}
.slot-table tr:last-child td {
    border-bottom: none;
}
.slot-table tr.row-istirahat td {
    background: rgba(245, 158, 11, 0.08);
    color: #b45309;
    font-weight: 600;
}
[data-theme="dark"] .slot-table tr.row-istirahat td {
    background: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
}
.tab-pills-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    border-bottom: 2px solid var(--border);
    padding-bottom: 8px;
}
.tab-pill-btn {
    background: transparent;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13.5px;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}
.tab-pill-btn:hover {
    color: var(--navy-primary);
    background: var(--badge-navy-bg);
}
.tab-pill-btn.active {
    background: #2563eb;
    color: #ffffff;
}
.tab-pane {
    display: none;
}
.tab-pane.active {
    display: block;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan Jam Sekolah</h1>
        <p class="page-subtitle">Konfigurasi Terpusat Jam Masuk, Jam Pulang, dan Alokasi Jam KBM Terhubung ke Seluruh Role</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <form action="{{ route('admin.jam-sekolah.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh jam sekolah dan alokasi KBM ke standar awal reguler?');">
            @csrf
            <button type="submit" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.2"/></svg>
                Reset ke Standar KBM
            </button>
        </form>
    </div>
</div>

{{-- Banner Info Keterhubungan Antar Role --}}
<div class="jam-card-hero">
    <div>
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #93c5fd; font-weight: 700; margin-bottom: 4px;">
            INTEGRASI SISTEM REAL-TIME
        </div>
        <div style="font-size: 20px; font-weight: 800; margin-bottom: 6px;">
            Pengaturan Jam Terhubung Otomatis ke Seluruh Role
        </div>
        <div style="font-size: 13.5px; color: #e2e8f0; max-width: 650px; line-height: 1.5;">
            Setiap perubahan waktu pada form di bawah akan langsung menyinkronkan <strong>Live Clock Banner</strong> di semua role, validasi keterlambatan siswa/guru, jam presensi masuk & keluar, toleransi piket kelas kosong, serta jadwal pelajaran.
        </div>
    </div>
    <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; padding: 14px 20px; text-align: center;">
        <div style="font-size: 11px; text-transform: uppercase; color: #bfdbfe; font-weight: 700;">Jam Masuk Aktif</div>
        <div style="font-size: 24px; font-weight: 800; font-family: monospace; margin: 2px 0;">{{ $jamMasuk }} WIB</div>
        <div style="font-size: 12px; color: #86efac; font-weight: 600;">Toleransi: +{{ $toleransiTerlambat }} Menit</div>
    </div>
</div>

<form action="{{ route('admin.jam-sekolah.update') }}" method="POST">
    @csrf

    <div class="jam-grid-settings">
        {{-- BLOK 1: JAM OPERASIONAL UTAMA --}}
        <div class="jam-section-box">
            <h3 class="jam-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                1. Jam Masuk & Pulang Sekolah
            </h3>

            <div class="form-group mb-16">
                <label class="form-label" for="jam_masuk">Jam Masuk Sekolah <span class="req">*</span></label>
                <input type="time" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $jamMasuk) }}" class="form-control" required>
                <small class="text-muted">Waktu mulai apel/KBM pagi hari untuk seluruh sekolah.</small>
            </div>

            <div class="form-row mb-16">
                <div class="form-group">
                    <label class="form-label" for="jam_pulang">Jam Pulang (Senin - Kamis) <span class="req">*</span></label>
                    <input type="time" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $jamPulang) }}" class="form-control" required>
                    <small class="text-muted">Akhir KBM Senin - Kamis.</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_pulang_jumat">Jam Pulang (Jumat) <span class="req">*</span></label>
                    <input type="time" id="jam_pulang_jumat" name="jam_pulang_jumat" value="{{ old('jam_pulang_jumat', $jamPulangJumat) }}" class="form-control" required>
                    <small class="text-muted">Akhir KBM hari Jumat.</small>
                </div>
            </div>
        </div>

        {{-- BLOK 2: DURASI JP & TOLERANSI --}}
        <div class="jam-section-box">
            <h3 class="jam-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                2. Durasi Jam Pelajaran (JP) & Toleransi
            </h3>

            <div class="form-row mb-16">
                <div class="form-group">
                    <label class="form-label" for="durasi_pelajaran_menit">Durasi 1 JP (Senin - Kamis)</label>
                    <div class="d-flex align-center gap-8">
                        <input type="number" id="durasi_pelajaran_menit" name="durasi_pelajaran_menit" value="{{ old('durasi_pelajaran_menit', $durasiPelajaran) }}" min="1" max="120" required class="form-control" style="width: 110px;">
                        <span class="text-muted fw-bold">menit</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="durasi_pelajaran_jumat_menit">Durasi 1 JP (Jumat)</label>
                    <div class="d-flex align-center gap-8">
                        <input type="number" id="durasi_pelajaran_jumat_menit" name="durasi_pelajaran_jumat_menit" value="{{ old('durasi_pelajaran_jumat_menit', $durasiPelajaranJumat) }}" min="1" max="120" required class="form-control" style="width: 110px;">
                        <span class="text-muted fw-bold">menit</span>
                    </div>
                </div>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="toleransi_keterlambatan_menit">Toleransi Keterlambatan Masuk</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="toleransi_keterlambatan_menit" name="toleransi_keterlambatan_menit" value="{{ old('toleransi_keterlambatan_menit', $toleransiTerlambat) }}" min="0" max="60" required class="form-control" style="width: 110px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
                <small class="text-muted">Batas menit sebelum siswa/guru otomatis tercatat sebagai "Terlambat".</small>
            </div>
        </div>

        {{-- BLOK 3: ATURAN JURNAL & PIKET --}}
        <div class="jam-section-box">
            <h3 class="jam-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                3. Batas Jurnal Guru & Piket
            </h3>

            <div class="form-group mb-16">
                <label class="form-label" for="batas_waktu_jurnal_menit">Batas Pengisian Jurnal Mengajar Guru</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="batas_waktu_jurnal_menit" name="batas_waktu_jurnal_menit" value="{{ old('batas_waktu_jurnal_menit', $batasWaktuJurnal) }}" min="0" required class="form-control" style="width: 110px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
                <small class="text-muted">Toleransi pengisian jurnal harian setelah sesi KBM guru berakhir.</small>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="toleransi_kelas_kosong_menit">Toleransi Peringatan Kelas Kosong (Piket)</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="toleransi_kelas_kosong_menit" name="toleransi_kelas_kosong_menit" value="{{ old('toleransi_kelas_kosong_menit', $toleransiKelasKosong) }}" min="0" required class="form-control" style="width: 110px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
                <small class="text-muted">Waktu sebelum sistem piket membunyikan peringatan kelas belum diisi guru.</small>
            </div>
        </div>
    </div>

    {{-- BLOK 4: TABEL DETAIL SLOT JAM PELAJARAN (INTERAKTIF) --}}
    <div class="card mb-24">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="card-title">Tabel & Editor Slot Jam KBM Real-Time</h3>
                <p class="card-subtitle">Pratinjau detail alokasi waktu per jam pelajaran (Senin–Kamis & Jumat)</p>
            </div>
            <div>
                @if($isCustomSeninKamis || $isCustomJumat)
                    <span class="badge badge-info">Menggunakan Slot Kustom</span>
                @else
                    <span class="badge badge-success">✓ Standar KBM Reguler</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="tab-pills-nav">
                <button type="button" class="tab-pill-btn active" onclick="switchJamTab('senin_kamis', this)">
                    Senin — Kamis (Jam 1 s.d 10)
                </button>
                <button type="button" class="tab-pill-btn" onclick="switchJamTab('jumat', this)">
                    Jumat (Jam 1 s.d 13)
                </button>
            </div>

            {{-- TAB 1: SENIN - KAMIS --}}
            <div id="tabPane_senin_kamis" class="tab-pane active">
                <div class="slot-table-wrapper">
                    <table class="slot-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Jam Ke</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                                <th>Keterangan / Aktivitas Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seninKamisSlots as $jam => $slot)
                                <tr>
                                    <td><strong class="text-navy">Jam Ke-{{ $jam }}</strong></td>
                                    <td>
                                        <input type="time" name="slots_senin_kamis[{{ $jam }}][mulai]" value="{{ $slot['waktu_mulai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time" name="slots_senin_kamis[{{ $jam }}][selesai]" value="{{ $slot['waktu_selesai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="text" name="slots_senin_kamis[{{ $jam }}][keterangan]" value="{{ $slot['keterangan'] ?? '' }}" placeholder="Opsional (misal: Upacara/Apel)" class="form-control form-control-sm">
                                    </td>
                                </tr>
                                @if(isset($seninKamisIstirahat[$jam]))
                                    <tr class="row-istirahat">
                                         <td>Istirahat</td>
                                         <td colspan="3">
                                             <strong>{{ $seninKamisIstirahat[$jam]['label'] }}</strong> ({{ $seninKamisIstirahat[$jam]['waktu'] }})
                                         </td>
                                     </tr>
                                 @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: JUMAT --}}
            <div id="tabPane_jumat" class="tab-pane">
                <div class="slot-table-wrapper">
                    <table class="slot-table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Jam Ke</th>
                                <th style="width: 160px;">Waktu Mulai</th>
                                <th style="width: 160px;">Waktu Selesai</th>
                                <th>Keterangan / Aktivitas Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jumatSlots as $jam => $slot)
                                <tr>
                                    <td><strong class="text-navy">Jam Ke-{{ $jam }}</strong></td>
                                    <td>
                                        <input type="time" name="slots_jumat[{{ $jam }}][mulai]" value="{{ $slot['waktu_mulai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="time" name="slots_jumat[{{ $jam }}][selesai]" value="{{ $slot['waktu_selesai'] }}" class="form-control form-control-sm" style="width: 130px;">
                                    </td>
                                    <td>
                                        <input type="text" name="slots_jumat[{{ $jam }}][keterangan]" value="{{ $slot['keterangan'] ?? '' }}" placeholder="Opsional (misal: Pembiasaan Hari Jumat)" class="form-control form-control-sm">
                                    </td>
                                </tr>
                                @if(isset($jumatIstirahat[$jam]))
                                    <tr class="row-istirahat">
                                        <td>Istirahat</td>
                                        <td colspan="3">
                                             <strong>{{ $jumatIstirahat[$jam]['label'] }}</strong> ({{ $jumatIstirahat[$jam]['waktu'] }})
                                        </td>
                                    </tr>
                                 @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL SUBMIT --}}
    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 40px;">
        <button type="submit" class="btn btn-primary btn-lg" style="padding: 12px 28px; font-weight: 700; font-size: 15px; box-shadow: var(--shadow-md);">
            SIMPAN PENGATURAN JAM SEKOLAH
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">
            Batal
        </a>
    </div>

</form>

<script>
function switchJamTab(tabKey, btnEl) {
    document.querySelectorAll('.tab-pill-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

    if (btnEl) btnEl.classList.add('active');
    const targetPane = document.getElementById('tabPane_' + tabKey);
    if (targetPane) targetPane.classList.add('active');
}
</script>
@endsection