@extends('layouts.app')

@section('title', 'Tambah Mapel — Jurnal Sekolah')
@section('page-title', 'Tambah Mapel Baru')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Mata Pelajaran Baru</h1>
        <p class="page-subtitle">Formulir Pendaftaran Master Mata Pelajaran</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Formulir Data Mapel</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('mapel.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nama_mapel">Nama Mata Pelajaran <span class="req">*</span></label>
                <input type="text" id="nama_mapel" name="nama_mapel" value="{{ old('nama_mapel') }}" class="form-control" placeholder="Contoh: Bahasa Indonesia" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="tingkat">Tingkat Kelas <span class="req">*</span></label>
                <select id="tingkat" name="tingkat" class="form-control" required>
                    <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>Kelas X (Kode: -01)</option>
                    <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI (Kode: -02)</option>
                    <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII (Kode: -03)</option>
                </select>
                <small class="form-hint" style="color:var(--text-muted, #666);font-size:12px;display:block;margin-top:4px;">
                    Contoh pengelompokan kode: Kelas 10 &rarr; <code>bin-01</code>, Kelas 11 &rarr; <code>bin-02</code>, Kelas 12 &rarr; <code>bin-03</code>
                </small>
            </div>

            <div class="form-group">
                <label class="form-label" for="kode_mapel">Kode Mapel (Opsional / Otomatis)</label>
                <input type="text" id="kode_mapel" name="kode_mapel" value="{{ old('kode_mapel') }}" class="form-control" placeholder="Kosongkan untuk dibuatkan otomatis, misal: bin-01">
                <div id="previewKodeWrap" style="margin-top:6px;font-size:13px;color:var(--primary, #0d6efd);">
                    Preview Kode Otomatis: <strong id="previewKodeText">-</strong>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const namaEl = document.getElementById('nama_mapel');
                const tingkatEl = document.getElementById('tingkat');
                const kodeEl = document.getElementById('kode_mapel');
                const previewEl = document.getElementById('previewKodeText');

                function updatePreview() {
                    if (kodeEl.value.trim() !== '') {
                        previewEl.textContent = kodeEl.value.trim() + ' (Custom)';
                        return;
                    }
                    const nama = namaEl.value.trim().toLowerCase();
                    const tingkat = tingkatEl.value;
                    if (!nama) {
                        previewEl.textContent = '-';
                        return;
                    }

                    const suffix = tingkat === 'X' ? '01' : (tingkat === 'XI' ? '02' : '03');
                    const known = {
                        'bahasa indonesia': 'bin',
                        'bahasa inggris': 'bing',
                        'bahasa jawa': 'bjw',
                        'bahasa jepang': 'bjp',
                        'matematika': 'mat',
                        'sejarah': 'sej',
                        'pendidikan pancasila': 'pp',
                        'pendidikan agama islam': 'pai',
                        'pendidikan agama islam dan budi pekerti': 'pai',
                        'pendidikan jasmani, olahraga dan kesehatan': 'pjok',
                        'seni budaya': 'sb',
                        'informatika': 'inf',
                        'projek ilmu pengetahuan alam dan sosial': 'ipas',
                        'kreativitas, inovasi, dan kewirausahaan': 'pkwu',
                        'bimbingan konseling': 'bk',
                        'bk': 'bk',
                        'dasar program keahlian': 'dpk',
                        'konsentrasi keahlian': 'kk',
                        'mata pelajaran pilihan': 'mpp'
                    };

                    let prefix = known[nama];
                    if (!prefix) {
                        const words = nama.split(/\s+/).filter(w => !['dan','&','atau','ke'].includes(w));
                        if (words.length > 1) {
                            prefix = words.map(w => w[0]).join('').slice(0, 4);
                        } else {
                            prefix = nama.slice(0, 3);
                        }
                    }
                    prefix = (prefix || 'mp').replace(/[^a-z0-9]/g, '');
                    previewEl.textContent = prefix + '-' + suffix;
                }

                namaEl.addEventListener('input', updatePreview);
                tingkatEl.addEventListener('change', updatePreview);
                kodeEl.addEventListener('input', updatePreview);
                updatePreview();
            });
            </script>

            <div class="d-flex gap-8 mt-24">
                <button type="submit" class="btn btn-primary">SIMPAN MAPEL</button>
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
