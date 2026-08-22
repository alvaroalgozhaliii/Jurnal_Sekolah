@extends('layouts.app')

@section('title', 'Buat Pengajuan Dispen / Izin — Jurnal Sekolah')
@section('page-title', 'Form Pengajuan Dispen / Izin')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            @if(Auth::user()->isPiket())
                Form Pengajuan Dispen Siswa (Piket)
            @elseif(Auth::user()->isGuru())
                Form Pengajuan Dispen / Izin Guru
            @else
                Form Pengajuan Izin / Dispensasi
            @endif
        </h1>
        <p class="page-subtitle">
            @if(Auth::user()->isPiket())
                Pencatatan pengajuan izin/dispen siswa secara offline yang diteruskan ke Waka
            @else
                Pengajuan izin atau dispensasi kegiatan belajar mengajar
            @endif
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Formulir Data Pengajuan
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Kategori Pengajuan -->
            <div class="form-group">
                <label class="form-label" for="kategori">Kategori Pengajuan <span class="req">*</span></label>
                <select id="kategori" name="kategori" class="form-control" required>
                    @if(Auth::user()->isGuru() && !Auth::user()->isPiket() && !Auth::user()->isAdmin())
                        <option value="izin_guru" {{ old('kategori') == 'izin_guru' ? 'selected' : '' }}>Dispen / Izin Meninggalkan Tugas Guru</option>
                    @else
                        <option value="dispensasi" {{ old('kategori') == 'dispensasi' ? 'selected' : '' }}>Dispensasi Pelajaran / Kegiatan Sekolah</option>
                        <option value="izin_keluar" {{ old('kategori') == 'izin_keluar' ? 'selected' : '' }}>Izin Keluar Lingkungan Sekolah</option>
                        <option value="izin_masuk" {{ old('kategori') == 'izin_masuk' ? 'selected' : '' }}>Izin Masuk / Terlambat</option>
                        <option value="sakit" {{ old('kategori') == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                        @if(Auth::user()->isAdmin() || Auth::user()->isPiket())
                        <option value="izin_guru" {{ old('kategori') == 'izin_guru' ? 'selected' : '' }}>Dispen Guru</option>
                        @endif
                    @endif
                </select>
            </div>

            <!-- Pilih Siswa (untuk Piket, Admin, Ortu) -->
            @if(!Auth::user()->isGuru() || Auth::user()->isPiket() || Auth::user()->isAdmin())
            <div class="form-group" id="group-pilih-siswa">
                <label class="form-label" for="id_siswa">Pilih Siswa <span class="req">*</span></label>
                <select id="id_siswa" name="id_siswa" class="form-control">
                    <option value="">-- Pilih Siswa (Nama / NIS / Kelas) --</option>
                    @foreach($siswas as $s)
                    <option value="{{ $s->id_siswa }}" {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                        {{ $s->nama }} (NIS: {{ $s->nis }} | Kelas: {{ $s->kelas->nama_kelas ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tanggal">Tanggal Pengajuan <span class="req">*</span></label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jenis_izin">Jenis Dispen / Keperluan</label>
                    <input type="text" id="jenis_izin" name="jenis_izin" value="{{ old('jenis_izin') }}" class="form-control" placeholder="Contoh: Lomba / Kegiatan OSIS / Urusan Keluarga">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jam_mulai">Jam Keluar / Mulai</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="perkiraan_kembali">Perkiraan Jam Kembali (Jika Ada)</label>
                    <input type="time" id="perkiraan_kembali" name="perkiraan_kembali" value="{{ old('perkiraan_kembali') }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="alasan">Alasan Lengkap Dispen / Izin <span class="req">*</span></label>
                <textarea id="alasan" name="alasan" rows="3" class="form-control" required placeholder="Jelaskan alasan pengajuan secara rinci">{{ old('alasan') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="keterangan">Keterangan Tambahan (Opsional)</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="form-control" placeholder="Catatan tambahan dari pemohon / guru piket...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="lampiran_foto">Lampiran Bukti / Surat (Opsional, Max 2MB)</label>
                <input type="file" id="lampiran_foto" name="lampiran_foto" accept="image/*" class="form-control">
            </div>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    KIRIM PENGAJUAN KE WAKA
                </button>
                <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
