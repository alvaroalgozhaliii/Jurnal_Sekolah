@extends('layouts.app')

@section('title', 'Buat Pengajuan Dispen / Izin — Jurnal Sekolah')
@section('page-title', 'Form Pengajuan Dispen / Izin')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            @if(Auth::user()->isPiket())
                Form Pengajuan Dispen (Piket)
            @elseif(Auth::user()->isGuru())
                Form Pengajuan Dispen / Izin Guru
            @else
                Form Pengajuan Izin / Dispensasi
            @endif
        </h1>
        <p class="page-subtitle">
            Pencatatan pengajuan dispensasi siswa atau guru yang diteruskan secara otomatis untuk verifikasi & persetujuan
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 760px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Formulir Data Pengajuan
        </h3>
    </div>
    <div class="card-body">
        
        @if(isset($wakaHariIni) && $wakaHariIni && $wakaHariIni->waka)
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; font-size:12.5px;">
            <div>
                🛡️ <strong>Waka Bertugas Hari Ini:</strong> {{ $wakaHariIni->waka->nama }} ({{ strtoupper(str_replace('_', ' ', $wakaHariIni->waka->role)) }})
            </div>
            @if($wakaHariIni->waka->no_hp)
                <span style="color:#059669; font-weight:600;">WA Aktif: {{ $wakaHariIni->waka->no_hp }}</span>
            @endif
        </div>
        @endif

        @if(Auth::user()->isPiket() || Auth::user()->isAdmin())
        <!-- TAB PILIH JENIS SUBJEK KHUSUS PIKET -->
        @php
            $initialType = old('kategori') === 'izin_guru' ? 'guru' : request('tipe', 'siswa');
        @endphp
        <div style="display:flex; gap:12px; margin-bottom:20px; background:var(--clr-bg-subtle); padding:6px; border-radius:8px;">
            <button type="button" id="btn-tab-siswa" onclick="setSubjekType('siswa')" class="btn" style="flex:1; justify-content:center; font-weight:600; padding:10px 14px; border-radius:6px; {{ $initialType === 'siswa' ? 'background:#1e3a8a; color:#fff;' : 'background:transparent; color:#334155; border:none;' }}">
                👨‍🎓 Dispensasi Siswa (Alur: Piket &rarr; Waka &rarr; Satpam)
            </button>
            <button type="button" id="btn-tab-guru" onclick="setSubjekType('guru')" class="btn" style="flex:1; justify-content:center; font-weight:600; padding:10px 14px; border-radius:6px; {{ $initialType === 'guru' ? 'background:#d97706; color:#fff;' : 'background:transparent; color:#334155; border:none;' }}">
                👨‍🏫 Dispensasi Guru (Alur: Piket &rarr; Waka SDM/Piket &rarr; Kepsek)
            </button>
        </div>
        @endif

        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="form-pengajuan">
            @csrf

            <!-- FIELD KATEGORI YANG PASTI TERKIRIM -->
            <input type="hidden" name="kategori" id="final_kategori" value="{{ old('kategori', (isset($initialType) && $initialType === 'guru') ? 'izin_guru' : 'dispensasi') }}">

            <!-- Kategori Pengajuan (Siswa / Guru) -->
            <div class="form-group mb-16" id="group-kategori-siswa">
                <label class="form-label" for="kategori_siswa">Kategori Dispensasi Siswa <span class="req">*</span></label>
                <select id="kategori_siswa" class="form-control" onchange="syncKategori()">
                    <option value="dispensasi" {{ old('kategori', 'dispensasi') == 'dispensasi' ? 'selected' : '' }}>Dispensasi Siswa (Pelajaran / Lomba / OSIS)</option>
                    <option value="izin_keluar" {{ old('kategori') == 'izin_keluar' ? 'selected' : '' }}>Izin Keluar Lingkungan Sekolah</option>
                    <option value="izin_masuk" {{ old('kategori') == 'izin_masuk' ? 'selected' : '' }}>Izin Masuk / Terlambat</option>
                    <option value="sakit" {{ old('kategori') == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                </select>
            </div>

            <div class="form-group mb-16" id="group-kategori-guru" style="display:none;">
                <label class="form-label" for="kategori_guru">Kategori Izin Guru</label>
                <input type="text" class="form-control" value="Dispensasi / Izin Meninggalkan Tugas Guru" readonly style="background:#f8fafc; font-weight:600;">
                <small class="text-muted" style="display:block; margin-top:4px;">
                    ℹ️ Pengajuan ini akan diteruskan ke <strong>Waka SDM</strong>, lalu ke <strong>Kepala Sekolah</strong> (tanpa satpam).
                </small>
            </div>

            <!-- Pilih Siswa (untuk Dispen Siswa) -->
            <div class="form-group mb-16" id="group-pilih-siswa">
                <label class="form-label" for="id_siswa">Pilih Siswa <span class="req">*</span></label>
                <select id="id_siswa" name="id_siswa" class="form-control select-search" placeholder="Ketik Nama / NIS / Kelas Siswa...">
                    <option value="">-- Cari Nama / NIS / Kelas Siswa --</option>
                    @foreach($siswas as $s)
                    <option value="{{ $s->id_siswa }}" {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                        {{ $s->nama }} (NIS: {{ $s->nis }} | Kelas: {{ $s->kelas->nama_kelas ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Guru (untuk Dispen Guru) -->
            <div class="form-group mb-16" id="group-pilih-guru" style="display:none;">
                <label class="form-label" for="id_guru">Pilih Guru yang Meminta Dispen <span class="req">*</span></label>
                <select id="id_guru" name="id_guru" class="form-control select-search" placeholder="Ketik Nama Guru / NIP / Bidang Studi...">
                    <option value="">-- Cari Nama Guru / NIP / Bidang Studi --</option>
                    @foreach($gurus as $g)
                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama }} (NIP: {{ $g->nip ?? '-' }} | {{ $g->bidang_studi ?? 'Guru' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tanggal">Tanggal Dispen <span class="req">*</span></label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jenis_izin">Keperluan / Jenis Dispen</label>
                    <input type="text" id="jenis_izin" name="jenis_izin" value="{{ old('jenis_izin') }}" class="form-control" placeholder="Contoh: Dinas Luar / MGMP / Lomba / Urusan Keluarga">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jam_mulai">Jam Keluar / Mulai</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="perkiraan_kembali">Perkiraan Jam Kembali</label>
                    <input type="time" id="perkiraan_kembali" name="perkiraan_kembali" value="{{ old('perkiraan_kembali') }}" class="form-control">
                </div>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="alasan">Alasan Lengkap Dispen <span class="req">*</span></label>
                <textarea id="alasan" name="alasan" rows="3" class="form-control" required placeholder="Jelaskan detail alasan atau surat tugas...">{{ old('alasan') }}</textarea>
            </div>

            <div class="form-group mb-16">
                <label class="form-label" for="keterangan">Keterangan Tambahan / Penugasan (Opsional)</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="form-control" placeholder="Tugas pengganti KBM untuk siswa / catatan tambahan piket...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="form-group mb-24">
                <label class="form-label" for="lampiran_foto">Lampiran Bukti / Surat Tugas (Opsional, Max 2MB)</label>
                <input type="file" id="lampiran_foto" name="lampiran_foto" accept="image/*,.pdf" class="form-control">
            </div>

            <!-- TOMBOL KIRIM PENGAJUAN -->
            <div style="display:flex; gap:12px; align-items:center; margin-top:28px; padding-top:20px; border-top:2px solid #e2e8f0;">
                <button type="submit" id="btn-submit" class="btn" style="background:#1e3a8a; color:#ffffff; padding:12px 24px; font-size:13.5px; font-weight:700; border-radius:6px; border:none; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    TERUSKAN DISPEN SISWA KE WAKA
                </button>
                <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary" style="padding:11px 20px; font-size:13.5px; border-radius:6px;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let currentSubjek = "{{ $initialType ?? 'siswa' }}";

function syncKategori() {
    const finalKat = document.getElementById('final_kategori');
    const kategoriSiswaSelect = document.getElementById('kategori_siswa');
    if (currentSubjek === 'guru') {
        finalKat.value = 'izin_guru';
    } else {
        finalKat.value = kategoriSiswaSelect.value;
    }
}

function setSubjekType(type) {
    currentSubjek = type;
    const btnSiswa = document.getElementById('btn-tab-siswa');
    const btnGuru = document.getElementById('btn-tab-guru');
    const groupKategoriSiswa = document.getElementById('group-kategori-siswa');
    const groupKategoriGuru = document.getElementById('group-kategori-guru');
    const groupSiswa = document.getElementById('group-pilih-siswa');
    const groupGuru = document.getElementById('group-pilih-guru');
    const idSiswaSelect = document.getElementById('id_siswa');
    const idGuruSelect = document.getElementById('id_guru');
    const btnSubmit = document.getElementById('btn-submit');
    const finalKat = document.getElementById('final_kategori');

    if (type === 'guru') {
        if (btnGuru) {
            btnGuru.style.background = '#d97706';
            btnGuru.style.color = '#fff';
        }
        if (btnSiswa) {
            btnSiswa.style.background = 'transparent';
            btnSiswa.style.color = '#334155';
            btnSiswa.style.border = 'none';
        }

        if (groupKategoriSiswa) groupKategoriSiswa.style.display = 'none';
        if (groupKategoriGuru) groupKategoriGuru.style.display = 'block';
        if (groupSiswa) groupSiswa.style.display = 'none';
        if (groupGuru) groupGuru.style.display = 'block';

        finalKat.value = 'izin_guru';

        if (idSiswaSelect) idSiswaSelect.required = false;
        if (idGuruSelect) idGuruSelect.required = true;

        if (btnSubmit) {
            btnSubmit.innerHTML = `<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> TERUSKAN DISPEN GURU KE WAKA SDM`;
            btnSubmit.style.background = '#d97706';
            btnSubmit.style.color = '#ffffff';
        }
    } else {
        if (btnSiswa) {
            btnSiswa.style.background = '#1e3a8a';
            btnSiswa.style.color = '#fff';
        }
        if (btnGuru) {
            btnGuru.style.background = 'transparent';
            btnGuru.style.color = '#334155';
            btnGuru.style.border = 'none';
        }

        if (groupKategoriSiswa) groupKategoriSiswa.style.display = 'block';
        if (groupKategoriGuru) groupKategoriGuru.style.display = 'none';
        if (groupSiswa) groupSiswa.style.display = 'block';
        if (groupGuru) groupGuru.style.display = 'none';

        syncKategori();

        if (idSiswaSelect) idSiswaSelect.required = true;
        if (idGuruSelect) idGuruSelect.required = false;

        if (btnSubmit) {
            btnSubmit.innerHTML = `<svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> TERUSKAN DISPEN SISWA KE WAKA`;
            btnSubmit.style.background = '#1e3a8a';
            btnSubmit.style.color = '#ffffff';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setSubjekType("{{ $initialType ?? 'siswa' }}");
});
</script>
@endsection
