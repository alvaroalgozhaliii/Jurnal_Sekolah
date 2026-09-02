@extends('layouts.app')

@section('title', 'Tambah Jadwal — Jurnal Sekolah')
@section('page-title', 'Tambah Jadwal Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Jadwal Pelajaran</h1>
        <p class="page-subtitle">
            @if($selectedKelas)
                Kelas: <strong>{{ $selectedKelas->nama_kelas }}</strong>
            @else
                Formulir Pembuatan Jadwal Pelajaran
            @endif
        </p>
    </div>
    <div class="page-actions">
        @if($selectedKelas)
            <a href="{{ route('kelas.show', $selectedKelas->id_kelas) }}" class="btn btn-secondary">&larr; Kembali ke Detail Kelas</a>
        @else
            <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        @endif
    </div>
</div>

<div class="card" style="max-width: 680px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Jadwal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal.store') }}" method="POST" id="formJadwal">
            @csrf

            {{-- Redirect kembali ke kelas setelah simpan --}}
            @if($selectedKelas)
                <input type="hidden" name="redirect_to_kelas" value="{{ $selectedKelas->id_kelas }}">
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="hari">Hari <span class="req">*</span></label>
                    <select id="hari" name="hari" class="form-control" required onchange="updateJamOptions(); updateWaktuDisplay();">
                        <option value="">Pilih Hari</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                        <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_ke">Jam Ke <span class="req">*</span></label>
                    <select id="jam_ke" name="jam_ke" class="form-control" required onchange="updateWaktuDisplay();">
                        <option value="">Pilih Jam</option>
                        @for($i = 1; $i <= 13; $i++)
                        <option value="{{ $i }}" {{ old('jam_ke') == $i ? 'selected' : '' }}>Jam {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Tampilan Jam Pembelajaran Otomatis --}}
            <div class="form-group">
                <label class="form-label">Jam Pembelajaran</label>
                <div id="waktuDisplay" class="form-control-static">
                    <span id="waktuText">
                        @if(old('hari') && old('jam_ke'))
                            {{ \App\Services\KbmService::getLabelWaktu(old('hari'), (int)old('jam_ke')) }}
                        @else
                            — Pilih Hari dan Jam Ke terlebih dahulu —
                        @endif
                    </span>
                </div>
            </div>

            {{-- Kelas --}}
            <div class="form-group">
                <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                @if($selectedKelas)
                    <input type="hidden" id="id_kelas" name="id_kelas" value="{{ $selectedKelas->id_kelas }}" data-tingkat="{{ $selectedKelas->tingkat }}">
                    <div class="form-control-static">
                        {{ $selectedKelas->nama_kelas }} (Tingkat {{ $selectedKelas->tingkat }})
                    </div>
                @else
                    <select id="id_kelas" name="id_kelas" class="form-control select-search" required onchange="updateJamOptions();" placeholder="Ketik / Pilih Kelas">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" data-tingkat="{{ $k->tingkat }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan->nama_jurusan ?? '-' }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="id_mapel">Mata Pelajaran <span class="req">*</span></label>
                    <select id="id_mapel" name="mapel" class="form-control select-search" required placeholder="Ketik / Pilih Mata Pelajaran">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapel as $m)
                        <option value="{{ $m->nama_mapel }}" {{ old('mapel') == $m->nama_mapel ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="id_guru">Guru Pengajar <span class="req">*</span></label>
                    <select id="id_guru" name="id_guru" class="form-control select-search" required placeholder="Ketik / Pilih Guru Pengajar">
                        <option value="">Pilih Guru</option>
                        @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }} ({{ $g->nip ?: ($g->bidang_studi ?: 'Guru') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ruang">Ruang Kelas</label>
                    <input type="text" id="ruang" name="ruang" value="{{ old('ruang') }}" class="form-control" placeholder="Contoh: R.101">
                </div>
                <div class="form-group">
                    <label class="form-label" for="aktif">Status Jadwal</label>
                    <select id="aktif" name="aktif" class="form-control">
                        <option value="1" {{ old('aktif', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
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
                <button type="submit" class="btn btn-primary">SIMPAN JADWAL</button>
                @if($selectedKelas)
                    <a href="{{ route('kelas.show', $selectedKelas->id_kelas) }}" class="btn btn-secondary">Batal</a>
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
// Alokasi waktu KBM dari KbmService
const alokasiWaktu = {!! \App\Services\KbmService::getAllAlokasiAsJson() !!};

function getSelectedTingkat() {
    const kelasElem = document.getElementById('id_kelas');
    if (!kelasElem) return null;
    if (kelasElem.tagName === 'INPUT') {
        return kelasElem.getAttribute('data-tingkat') || '';
    }
    const selectedOpt = kelasElem.options[kelasElem.selectedIndex];
    return selectedOpt ? (selectedOpt.getAttribute('data-tingkat') || '') : '';
}

function updateJamOptions() {
    const hari = document.getElementById('hari').value;
    const jamKeSelect = document.getElementById('jam_ke');
    const tingkat = getSelectedTingkat();
    const isKelasX = (!tingkat) || tingkat.toUpperCase() === 'X' || tingkat.toUpperCase() === '10';

    // Sesuaikan opsi Jam 13 pada hari Jumat
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
    const tingkat = getSelectedTingkat();
    const isKelasX = (!tingkat) || tingkat.toUpperCase() === 'X' || tingkat.toUpperCase() === '10';

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

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    updateJamOptions();
});
</script>
@endpush