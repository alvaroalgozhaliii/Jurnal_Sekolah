@extends('layouts.app')

@section('title', 'Edit Mapel — Jurnal Sekolah')
@section('page-title', 'Edit Mapel')

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
.form-page-header .page-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0 0 3px; }
.form-page-header .page-subtitle { font-size: 13px; color: var(--text-muted); margin: 0; }

.form-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius-lg); box-shadow: var(--shadow-md);
    max-width: 640px; overflow: hidden;
    animation: fadeSlideUp 0.45s ease 0.1s both;
}
.form-card-header {
    padding: 18px 24px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(135deg, rgba(245,158,11,0.05), rgba(217,119,6,0.03));
}
.form-card-header-icon {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, #d97706, #b45309);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; flex-shrink: 0;
}
.form-card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0; }
.form-card-header p  { font-size: 12px; color: var(--text-muted); margin: 3px 0 0; }
.form-card-body { padding: 24px; }
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 7px; }
.form-label .req { color: #ef4444; margin-left: 2px; }
.form-label .hint { font-weight: 400; color: var(--text-muted); font-size: 11.5px; }
.form-control {
    width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
    border-radius: var(--radius-md); font-size: 13.5px;
    font-family: 'Inter', sans-serif; color: var(--text-primary);
    background: var(--bg-card); transition: all 0.2s ease; outline: none;
}
.form-control:hover { border-color: #93c5fd; }
.form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,0.15); }
.form-control.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
[data-theme="dark"] .form-control { background: rgba(255,255,255,0.05); color: #f8fafc; border-color: #334155; }

.tingkat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.tingkat-card-input { display: none; }
.tingkat-card-label {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 6px; padding: 14px 10px; border: 2px solid var(--border);
    border-radius: var(--radius-md); cursor: pointer; transition: all 0.2s ease;
    font-size: 13px; font-weight: 600; color: var(--text-secondary); background: var(--bg-card);
}
.tingkat-card-label:hover { border-color: #fbbf24; color: #d97706; background: rgba(217,119,6,0.04); }
.tingkat-card-label .tk-emoji { font-size: 22px; }
.tingkat-card-label .tk-label { font-size: 13.5px; font-weight: 700; }
.tingkat-card-label .tk-sub   { font-size: 11px; font-weight: 400; color: var(--text-muted); }
.tingkat-card-input[value="X"]:checked + .tingkat-card-label  { border-color: #059669; color: #059669; background: rgba(16,185,129,0.06); }
.tingkat-card-input[value="XI"]:checked + .tingkat-card-label { border-color: #d97706; color: #d97706; background: rgba(245,158,11,0.06); }
.tingkat-card-input[value="XII"]:checked + .tingkat-card-label{ border-color: #7c3aed; color: #7c3aed; background: rgba(139,92,246,0.06); }

.form-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }
.invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 5px; }

/* Current info badge */
.current-info-bar {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    background: rgba(245,158,11,0.06);
    border: 1px solid rgba(217,119,6,0.2);
    border-radius: var(--radius-md);
    margin-bottom: 22px;
    font-size: 13px;
    color: var(--text-secondary);
}
.current-info-bar strong { color: #d97706; }

.form-footer {
    display: flex; gap: 10px; align-items: center;
    margin-top: 28px; padding-top: 20px;
    border-top: 1px solid var(--border); flex-wrap: wrap;
}
</style>
@endpush

@section('content')

<div class="form-page-header">
    <div>
        <h1 class="page-title">Edit Mata Pelajaran</h1>
        <p class="page-subtitle">Memperbarui informasi: <strong>{{ $mapel->nama_mapel }}</strong></p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('mapel.show', $mapel->id_mapel) }}" class="btn btn-secondary">Detail</a>
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:5px;">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="white" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <div>
            <h3>Edit Formulir Mapel</h3>
            <p>Perubahan akan disimpan langsung ke database</p>
        </div>
    </div>

    <div class="form-card-body">

        {{-- Current Info --}}
        <div class="current-info-bar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#d97706" stroke-width="2" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Mengedit: <strong>{{ $mapel->nama_mapel }}</strong> —
            Kode: <strong>{{ $mapel->kode_mapel ?? '-' }}</strong> —
            Tingkat: <strong>Kelas {{ $mapel->tingkat ?? '-' }}</strong>
        </div>

        <form action="{{ route('mapel.update', $mapel->id_mapel) }}" method="POST" id="editMapelForm">
            @csrf @method('PUT')

            {{-- Nama Mapel --}}
            <div class="form-group">
                <label class="form-label" for="nama_mapel">
                    Nama Mata Pelajaran <span class="req">*</span>
                </label>
                <input type="text" id="nama_mapel" name="nama_mapel"
                    value="{{ old('nama_mapel', $mapel->nama_mapel) }}"
                    class="form-control {{ $errors->has('nama_mapel') ? 'is-invalid' : '' }}"
                    placeholder="Nama mata pelajaran..." required autocomplete="off">
                @error('nama_mapel')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tingkat --}}
            <div class="form-group">
                <label class="form-label">
                    Tingkat Kelas <span class="req">*</span>
                </label>
                <div class="tingkat-grid">
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_x" value="X"
                            class="tingkat-card-input"
                            {{ old('tingkat', $mapel->tingkat) === 'X' ? 'checked' : '' }}>
                        <label for="tingkat_x" class="tingkat-card-label">
                            <span class="tk-emoji">🟢</span>
                            <span class="tk-label">Kelas X</span>
                            <span class="tk-sub">Kode: -01</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_xi" value="XI"
                            class="tingkat-card-input"
                            {{ old('tingkat', $mapel->tingkat) === 'XI' ? 'checked' : '' }}>
                        <label for="tingkat_xi" class="tingkat-card-label">
                            <span class="tk-emoji">🟡</span>
                            <span class="tk-label">Kelas XI</span>
                            <span class="tk-sub">Kode: -02</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="tingkat" id="tingkat_xii" value="XII"
                            class="tingkat-card-input"
                            {{ old('tingkat', $mapel->tingkat) === 'XII' ? 'checked' : '' }}>
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
                    <span class="hint">— Kosongkan untuk mempertahankan kode saat ini</span>
                </label>
                <input type="text" id="kode_mapel" name="kode_mapel"
                    value="{{ old('kode_mapel', $mapel->kode_mapel) }}"
                    class="form-control {{ $errors->has('kode_mapel') ? 'is-invalid' : '' }}"
                    placeholder="Contoh: bin-01">
                @error('kode_mapel')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-hint">
                    Kode saat ini: <code>{{ $mapel->kode_mapel ?? 'Belum ada' }}</code>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary" id="submitBtn"
                    style="background:linear-gradient(135deg,#d97706,#b45309);border-color:#b45309;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:6px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('editMapelForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
});
</script>
@endpush
