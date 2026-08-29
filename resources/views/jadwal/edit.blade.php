@extends('layouts.app')

@section('title', 'Edit Jadwal — Jurnal Sekolah')
@section('page-title', 'Edit Jadwal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jadwal Pelajaran</h1>
        <p class="page-subtitle">Kelas: <strong>{{ $jadwal->kelas->nama_kelas ?? '-' }}</strong></p>
    </div>
    <div class="page-actions">
        @if($jadwal->id_kelas)
            <a href="{{ route('kelas.show', $jadwal->id_kelas) }}" class="btn btn-secondary">&larr; Kembali ke Detail Kelas</a>
        @else
            <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        @endif
    </div>
</div>

<div class="card" style="max-width: 680px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Edit Jadwal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal.update', $jadwal->id_jadwal) }}" method="POST" id="formJadwal">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="hari">Hari <span class="req">*</span></label>
                    <select id="hari" name="hari" class="form-control" required onchange="updateWaktuDisplay();">
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                        <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_ke">Jam Ke <span class="req">*</span></label>
                    <select id="jam_ke" name="jam_ke" class="form-control" required onchange="updateWaktuDisplay();">
                        <option value="">-- Pilih Jam --</option>
                        @for($i = 1; $i <= 13; $i++)
                        <option value="{{ $i }}" {{ old('jam_ke', $jadwal->jam_ke) == $i ? 'selected' : '' }}>Jam {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Tampilan Jam Pembelajaran Otomatis --}}
            <div class="form-group">
                <label class="form-label">Jam Pembelajaran</label>
                <div id="waktuDisplay" class="form-control-static" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; font-weight:600; color:#1e3a8a; min-height:42px;">
                    <span id="waktuText">
                        {{ \App\Services\KbmService::getLabelWaktu($jadwal->hari, $jadwal->jam_ke) ?: '— Pilih Hari dan Jam Ke —' }}
                    </span>
                </div>
            </div>

            {{-- Kelas (read-only dari data existing) --}}
            <div class="form-group">
                <label class="form-label">Kelas <span class="req">*</span></label>
                <input type="hidden" name="id_kelas" value="{{ $jadwal->id_kelas }}">
                <div class="form-control-static" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; font-weight:600; color:#1e3a8a;">
                    {{ $jadwal->kelas->nama_kelas ?? '—' }}
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_mapel">Mata Pelajaran <span class="req">*</span></label>
                    <select id="id_mapel" name="mapel" class="form-control select-search" required placeholder="Ketik / Pilih Mata Pelajaran">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapel as $m)
                        <option value="{{ $m->nama_mapel }}" {{ old('mapel', $jadwal->mapel) == $m->nama_mapel ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                        {{-- Fallback: jika mapel existing tidak ada di tabel, tampilkan tetap selected --}}
                        @if($jadwal->mapel && !$mapel->contains('nama_mapel', $jadwal->mapel))
                        <option value="{{ $jadwal->mapel }}" selected>{{ $jadwal->mapel }} (tidak terdaftar)</option>
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_guru">Guru Pengajar <span class="req">*</span></label>
                    <select id="id_guru" name="id_guru" class="form-control select-search" required placeholder="Ketik / Pilih Guru Pengajar">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ old('id_guru', $jadwal->id_guru) == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }} ({{ $g->nip ?: ($g->bidang_studi ?: 'Guru') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ruang">Ruang Kelas</label>
                    <input type="text" id="ruang" name="ruang" value="{{ old('ruang', $jadwal->ruang) }}" class="form-control" placeholder="Contoh: R.101">
                </div>
                <div class="form-group">
                    <label class="form-label" for="aktif">Status</label>
                    <select id="aktif" name="aktif" class="form-control">
                        <option value="1" {{ old('aktif', $jadwal->aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif', $jadwal->aktif) == 0 ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">UPDATE JADWAL</button>
                @if($jadwal->id_kelas)
                    <a href="{{ route('kelas.show', $jadwal->id_kelas) }}" class="btn btn-secondary">Batal</a>
                @else
                    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Batal</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const alokasiWaktu = {!! \App\Services\KbmService::getAllAlokasiAsJson() !!};
const tingkatKelas = "{{ $jadwal->kelas->tingkat ?? '' }}";
const isKelasX = (!tingkatKelas) || tingkatKelas.toUpperCase() === 'X' || tingkatKelas.toUpperCase() === '10';

function updateJamOptions() {
    const hari = document.getElementById('hari').value;
    const jamKeSelect = document.getElementById('jam_ke');

    for (let i = 0; i < jamKeSelect.options.length; i++) {
        const opt = jamKeSelect.options[i];
        if (opt.value === '13') {
            if (hari === 'Jumat' && !isKelasX) {
                opt.text = 'Jam 13 (Khusus Kelas X)';
                opt.disabled = true;
                if (jamKeSelect.value === '13') {
                    jamKeSelect.value = '';
                }
            } else {
                opt.text = 'Jam 13';
                opt.disabled = false;
            }
        }
    }

    updateWaktuDisplay();
}

function updateWaktuDisplay() {
    const hari = document.getElementById('hari').value;
    const jamKe = document.getElementById('jam_ke').value;
    const waktuText = document.getElementById('waktuText');

    if (!hari || !jamKe) {
        waktuText.textContent = '— Pilih Hari dan Jam Ke terlebih dahulu —';
        return;
    }

    const hariKey = hari.toLowerCase();
    const jam = parseInt(jamKe);

    // Cek khusus Jam 13 Jumat
    if (hari === 'Jumat' && jam === 13 && !isKelasX) {
        waktuText.innerHTML = '<span style="color:#dc2626;">Jam 13 pada hari Jumat hanya berlaku untuk Kelas X (Kelas 10).</span>';
        return;
    }

    const alokasi = (alokasiWaktu[hariKey] && alokasiWaktu[hariKey][jam])
        ? alokasiWaktu[hariKey][jam]
        : null;

    if (alokasi) {
        waktuText.textContent = 'Jam ' + jam + ' — ' + alokasi;
    } else {
        waktuText.innerHTML = '<span style="color:#dc2626;">Jam ' + jam + ' tidak tersedia pada hari ' + hari + '</span>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateJamOptions();
});
</script>
@endpush