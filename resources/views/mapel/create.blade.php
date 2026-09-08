@extends('layouts.app')

@section('title', 'Tambah Mapel — Jurnal Sekolah')
@section('page-title', 'Tambah Mapel Baru')

@push('styles')
<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.form-page-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    animation: fadeSlideUp 0.4s ease both;
}
.form-page-header .page-title {
    font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0 0 3px;
}
.form-page-header .page-subtitle { font-size: 13px; color: var(--text-muted); margin: 0; }

.form-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    max-width: 640px;
    overflow: hidden;
    animation: fadeSlideUp 0.45s ease 0.1s both;
}
.form-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(135deg, rgba(37,99,235,0.05), rgba(29,78,216,0.03));
}
.form-card-header-icon {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white;
    flex-shrink: 0;
}
.form-card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0; }
.form-card-header p  { font-size: 12px; color: var(--text-muted); margin: 3px 0 0; }

.form-card-body { padding: 24px; }

.form-group { margin-bottom: 20px; }
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 7px;
}
.form-label .req { color: #ef4444; margin-left: 2px; }
.form-label .hint { font-weight: 400; color: var(--text-muted); font-size: 11.5px; }

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13.5px;
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
    background: var(--bg-card);
    transition: all 0.2s ease;
    outline: none;
}
.form-control:hover  { border-color: #93c5fd; }
.form-control:focus  { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
.form-control.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }

[data-theme="dark"] .form-control {
    background: rgba(255,255,255,0.05);
    color: #f8fafc;
    border-color: #334155;
}
[data-theme="dark"] .form-control:focus { border-color: #60a5fa; }

.tingkat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.tingkat-card-input { display: none; }
.tingkat-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 14px 10px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    background: var(--bg-card);
}
.tingkat-card-label:hover { border-color: #93c5fd; color: #2563eb; background: rgba(37,99,235,0.04); }
.tingkat-card-label .tk-emoji { font-size: 22px; }
.tingkat-card-label .tk-label { font-size: 13.5px; font-weight: 700; }
.tingkat-card-label .tk-sub   { font-size: 11px; font-weight: 400; color: var(--text-muted); }

.tingkat-card-input:checked + .tingkat-card-label {
    border-width: 2px;
    background: rgba(37,99,235,0.06);
}
.tingkat-card-input[value="X"]:checked + .tingkat-card-label {
    border-color: #059669; color: #059669; background: rgba(16,185,129,0.06);
}
.tingkat-card-input[value="XI"]:checked + .tingkat-card-label {
    border-color: #d97706; color: #d97706; background: rgba(245,158,11,0.06);
}
.tingkat-card-input[value="XII"]:checked + .tingkat-card-label {
    border-color: #7c3aed; color: #7c3aed; background: rgba(139,92,246,0.06);
}

/* Kode preview box */
.kode-preview-box {
    margin-top: 10px;
    padding: 10px 14px;
    border: 1.5px dashed var(--border);
    border-radius: var(--radius-md);
    background: var(--bg-app);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text-secondary);
    transition: all 0.2s ease;
}
.kode-preview-box.has-value { border-color: #2563eb; border-style: solid; }
.kode-preview-box .preview-badge {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 14px;
    color: #2563eb;
    background: rgba(37,99,235,0.1);
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    transition: all 0.25s ease;
}

.form-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }

.form-footer {
    display: flex; gap: 10px; align-items: center;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}

.invalid-feedback {
    font-size: 12px;
    color: #ef4444;
    margin-top: 5px;
}
</style>
@endpush

@section('content')

<div class="form-page-header">
    <div>
        <h1 class="page-title">Tambah Mata Pelajaran Baru</h1>
        <p class="page-subtitle">Isi formulir berikut untuk menambahkan mata pelajaran ke kurikulum</p>
    </div>
    <a href="{{ route('mapel.index') }}" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="white" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
        </div>
        <div>
            <h3>Formulir Data Mapel</h3>
            <p>Semua field bertanda <span style="color:#ef4444;">*</span> wajib diisi</p>
        </div>
    </div>

    <div class="form-card-body">
        <form action="{{ route('mapel.store') }}" method="POST" id="mapelForm">
            @csrf

            {{-- Nama Mapel --}}
            <div class="form-group">
                <label class="form-label" for="nama_mapel">
                    Nama Mata Pelajaran <span class="req">*</span>
                </label>
                <input type="text" id="nama_mapel" name="nama_mapel"
                    value="{{ old('nama_mapel') }}"
                    class="form-control {{ $errors->has('nama_mapel') ? 'is-invalid' : '' }}"
                    placeholder="Contoh: Bahasa Indonesia, Matematika..." required autocomplete="off">
                @error('nama_mapel')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tingkat --}}
            <div class="form-group">
                <label class="form-label">
                    Tingkat Kelas <span class="req">*</span>
                    <span class="hint">— Pilih kelas yang menggunakan mapel ini</span>
                </label>
                <div class="tingkat-grid">
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_x" value="X"
                            class="tingkat-card-input"
                            {{ old('tingkat', 'X') === 'X' ? 'checked' : '' }}>
                        <label for="tingkat_x" class="tingkat-card-label">
                            <span class="tk-emoji">🟢</span>
                            <span class="tk-label">Kelas X</span>
                            <span class="tk-sub">Kode: -01</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_xi" value="XI"
                            class="tingkat-card-input"
                            {{ old('tingkat') === 'XI' ? 'checked' : '' }}>
                        <label for="tingkat_xi" class="tingkat-card-label">
                            <span class="tk-emoji">🟡</span>
                            <span class="tk-label">Kelas XI</span>
                            <span class="tk-sub">Kode: -02</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_xii" value="XII"
                            class="tingkat-card-input"
                            {{ old('tingkat') === 'XII' ? 'checked' : '' }}>
                        <label for="tingkat_xii" class="tingkat-card-label">
                            <span class="tk-emoji">🟣</span>
                            <span class="tk-label">Kelas XII</span>
                            <span class="tk-sub">Kode: -03</span>
                        </label>
                    </div>
                </div>
                @error('tingkat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kode Mapel --}}
            <div class="form-group">
                <label class="form-label" for="kode_mapel">
                    Kode Mapel
                    <span class="hint">— Biarkan kosong untuk dibuat otomatis</span>
                </label>
                <input type="text" id="kode_mapel" name="kode_mapel"
                    value="{{ old('kode_mapel') }}"
                    class="form-control {{ $errors->has('kode_mapel') ? 'is-invalid' : '' }}"
                    placeholder="Kosongkan untuk kode otomatis, atau isi manual: bin-01">
                @error('kode_mapel')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="kode-preview-box" id="kodePreviewBox">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Preview kode otomatis:</span>
                    <strong class="preview-badge" id="previewKodeText">-</strong>
                </div>
                <div class="form-hint">
                    Contoh: Kelas X → <code>bin-01</code>, Kelas XI → <code>bin-02</code>, Kelas XII → <code>bin-03</code>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:6px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Mata Pelajaran
                </button>
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const namaEl    = document.getElementById('nama_mapel');
    const kodeEl    = document.getElementById('kode_mapel');
    const previewEl = document.getElementById('previewKodeText');
    const boxEl     = document.getElementById('kodePreviewBox');
    const radios    = document.querySelectorAll('input[name="tingkat"]');

    function getTingkat() {
        for (const r of radios) { if (r.checked) return r.value; }
        return 'X';
    }

    const known = {
        'bahasa indonesia': 'bin', 'bahasa inggris': 'bing', 'bahasa jawa': 'bjw',
        'bahasa jepang': 'bjp', 'matematika': 'mat', 'sejarah': 'sej',
        'pendidikan pancasila': 'pp', 'pendidikan agama islam': 'pai',
        'pendidikan agama islam dan budi pekerti': 'pai',
        'pendidikan jasmani, olahraga dan kesehatan': 'pjok', 'seni budaya': 'sb',
        'informatika': 'inf', 'projek ilmu pengetahuan alam dan sosial': 'ipas',
        'kreativitas, inovasi, dan kewirausahaan': 'pkwu',
        'bimbingan konseling': 'bk', 'bk': 'bk', 'dasar program keahlian': 'dpk',
        'konsentrasi keahlian': 'kk', 'mata pelajaran pilihan': 'mpp'
    };

    function updatePreview() {
        if (kodeEl.value.trim() !== '') {
            previewEl.textContent = kodeEl.value.trim() + ' (Custom)';
            boxEl.classList.add('has-value');
            return;
        }
        const nama   = namaEl.value.trim().toLowerCase();
        const tingkat = getTingkat();
        if (!nama) { previewEl.textContent = '-'; boxEl.classList.remove('has-value'); return; }

        const suffix = tingkat === 'X' ? '01' : (tingkat === 'XI' ? '02' : '03');
        let prefix = known[nama];
        if (!prefix) {
            const words = nama.split(/\s+/).filter(w => !['dan','&','atau','ke'].includes(w));
            if (words.length > 1) { prefix = words.map(w => w[0]).join('').slice(0,4); }
            else { prefix = nama.slice(0,3); }
        }
        prefix = (prefix || 'mp').replace(/[^a-z0-9]/g, '');
        const result = prefix + '-' + suffix;
        previewEl.textContent = result;
        boxEl.classList.add('has-value');
    }

    namaEl.addEventListener('input', updatePreview);
    kodeEl.addEventListener('input', updatePreview);
    radios.forEach(r => r.addEventListener('change', updatePreview));
    updatePreview();

    // Submit feedback
    document.getElementById('mapelForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;animation:spin 0.8s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Menyimpan...';
    });
})();
</script>
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush
