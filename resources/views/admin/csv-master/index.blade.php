@extends('layouts.app')

@section('title', 'Pusat CSV Master & Import Data — Jurnal Sekolah')
@section('page-title', 'CSV Master & Import Data')

@section('content')
<style>
/* ── Layout ─────────────────────────────────────────── */
.csv-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 1024px) {
    .csv-layout { grid-template-columns: 1fr; }
}

/* ── Dropzone ────────────────────────────────────────── */
.dropzone-label {
    display: block;
    border: 2px dashed var(--border);
    border-radius: 10px;
    padding: 36px 24px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--bg-page);
    position: relative;
}
.dropzone-label:hover,
.dropzone-label.dragover {
    border-color: #3b82f6;
    background: rgba(59,130,246,.05);
}
.dropzone-icon-wrap {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: rgba(59,130,246,.12);
    color: #3b82f6;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
}
.dropzone-icon-wrap svg { width: 26px; height: 26px; }
.dropzone-title {
    font-size: 15px; font-weight: 700;
    color: var(--text-primary); margin: 0 0 4px;
}
.dropzone-sub { font-size: 12.5px; color: var(--text-muted); margin: 0; }
.dropzone-file-badge {
    display: none; margin-top: 12px;
    background: rgba(59,130,246,.12);
    color: #3b82f6; border-radius: 20px;
    padding: 4px 14px; font-size: 12.5px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
}

/* ── Alert box ───────────────────────────────────────── */
.csv-info-strip {
    background: rgba(59,130,246,.08);
    border: 1px solid rgba(59,130,246,.2);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12.5px;
    color: var(--text-secondary);
    display: flex; gap: 8px; align-items: flex-start;
    margin-top: 16px;
}

/* ── File download card ──────────────────────────────── */
.file-dl-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.file-dl-row:last-child { border-bottom: none; }
.file-dl-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    border-radius: 8px;
    background: rgba(59,130,246,.12);
    color: #3b82f6;
    display: flex; align-items: center; justify-content: center;
}
.file-dl-icon svg { width: 16px; height: 16px; }
.file-dl-body { flex: 1; min-width: 0; }
.file-dl-title { font-size: 12.5px; font-weight: 700; color: var(--text-primary); line-height: 1.4; }
.file-dl-sub   { font-size: 11px; color: var(--text-muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.file-dl-btn   { flex-shrink: 0; align-self: center; }

/* ── Stats row ───────────────────────────────────────── */
.stat-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 0; border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.stat-row:last-child { border-bottom: none; padding-bottom: 0; }
.stat-row-label { color: var(--text-secondary); }
.stat-row-val   { font-weight: 700; color: var(--text-primary); font-size: 14px; }

/* ── Role badge example ──────────────────────────────── */
.role-chip {
    display: inline-flex; align-items: center;
    padding: 2px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700; letter-spacing: .3px;
}
.role-chip.guru      { background: #dbeafe; color: #1e40af; }
.role-chip.walikelas { background: #d1fae5; color: #065f46; }
.role-chip.siswa     { background: #fef3c7; color: #92400e; }

/* ── Format table responsive ─────────────────────────── */
.format-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.format-table { width: 100%; font-size: 12px; border-collapse: collapse; min-width: 560px; }
.format-table th {
    background: var(--bg-page); color: var(--text-muted);
    font-size: 11px; text-transform: uppercase; letter-spacing: .5px;
    padding: 8px 12px; text-align: left; border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.format-table td {
    padding: 9px 12px; border-bottom: 1px solid var(--border);
    color: var(--text-primary); vertical-align: middle;
}
.format-table tr:last-child td { border-bottom: none; }
.format-table td code {
    background: var(--bg-page); padding: 1px 5px;
    border-radius: 4px; font-size: 11px; color: #ef4444;
}
</style>

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Pusat CSV Master & Import Data</h1>
        <p class="page-subtitle">Import & sinkronisasi data Guru, Siswa, dan Wali Kelas via CSV</p>
    </div>
</div>

{{-- ── Alert messages ───────────────────────────────────────── --}}
@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px;">
    <div>{{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:20px;">
    <div>{{ session('error') }}</div>
</div>
@endif

{{-- ── Two-column layout ────────────────────────────────────── --}}
<div class="csv-layout">

    {{-- ── LEFT: Upload + Format Guide ──────────────────────── --}}
    <div>

        {{-- Card Upload --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h3 class="card-title">📤 Upload File CSV Master</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.csv-master.import') }}" method="POST"
                      enctype="multipart/form-data" id="csvUploadForm">
                    @csrf

                    <label class="dropzone-label" for="csv_file_input" id="dropzone">
                        <div class="dropzone-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <p class="dropzone-title">Klik atau drag file CSV ke sini</p>
                        <p class="dropzone-sub">Mendukung .CSV (UTF-8) hingga 5 MB</p>
                        <span id="fileBadge" style="display:none; margin-top:12px; background:rgba(59,130,246,.12); color:#3b82f6; border-radius:20px; padding:4px 14px; font-size:12.5px; font-weight:600;">
                            📄 <span id="fileNameSpan"></span>
                        </span>
                        <input type="file" id="csv_file_input" name="csv_file"
                               accept=".csv,text/csv" required
                               style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;"
                               onchange="handleCsvPicked(this)">
                    </label>

                    <div class="csv-info-strip">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Data otomatis disortir ke tabel <strong>Guru</strong>, <strong>Siswa</strong>, dan <strong>Wali Kelas</strong> berdasarkan kolom <code style="background:rgba(59,130,246,.12);color:#3b82f6;border-radius:4px;padding:0 5px;">role</code>.
                    </div>

                    <div style="display:flex; justify-content:flex-end; margin-top:16px; gap:10px; flex-wrap:wrap;">
                        <a href="{{ route('admin.csv-master.template', 'template_master') }}"
                           class="btn btn-secondary" style="font-size:13px;">
                            ⬇️ Download Template
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            🚀 Proses & Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card Format Kolom --}}
        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <h3 class="card-title" style="margin:0;">📋 Format Kolom CSV Terpadu</h3>
                <span style="font-size:12px; color:var(--text-muted);">Scroll horizontal jika tabel terpotong →</span>
            </div>
            <div class="format-table-wrap">
                <table class="format-table">
                    <thead>
                        <tr>
                            <th>role</th>
                            <th>nama</th>
                            <th>nip_nisn</th>
                            <th>username</th>
                            <th>password</th>
                            <th>nama_kelas</th>
                            <th>mata_pelajaran</th>
                            <th>no_hp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="role-chip guru">guru</span></td>
                            <td>Dra. Anik Indriani</td>
                            <td><code>196811 200501 004</code></td>
                            <td>anik1968</td>
                            <td>password123</td>
                            <td>—</td>
                            <td>Sejarah</td>
                            <td>08123…</td>
                        </tr>
                        <tr>
                            <td><span class="role-chip walikelas">walikelas</span></td>
                            <td>Muashofah, M.Pd</td>
                            <td><code>197108 200801 012</code></td>
                            <td>muashofah</td>
                            <td>password123</td>
                            <td><strong>X TKI-1</strong></td>
                            <td>Pend. Agama Islam</td>
                            <td>08123…</td>
                        </tr>
                        <tr>
                            <td><span class="role-chip siswa">siswa</span></td>
                            <td>Ahmad Fauzan</td>
                            <td><code>0081234501</code></td>
                            <td>fauzan01</td>
                            <td>password123</td>
                            <td><strong>X TKI-1</strong></td>
                            <td>—</td>
                            <td>08512…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:14px 20px; background:var(--bg-page); border-top:1px solid var(--border); font-size:12.5px; color:var(--text-secondary);">
                <strong style="display:block; margin-bottom:6px; color:var(--text-primary);">📌 Keterangan Kolom:</strong>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:4px 20px; line-height:1.8;">
                    <span><code style="background:var(--bg-card-header); border-radius:4px; padding:0 5px; font-size:11px; color:#ef4444;">role</code> → <code style="font-size:11px;">guru</code> / <code style="font-size:11px;">walikelas</code> / <code style="font-size:11px;">siswa</code></span>
                    <span><code style="background:var(--bg-card-header); border-radius:4px; padding:0 5px; font-size:11px; color:#ef4444;">nama_kelas</code> → wajib untuk siswa & wali kelas</span>
                    <span><code style="background:var(--bg-card-header); border-radius:4px; padding:0 5px; font-size:11px; color:#ef4444;">nip_nisn</code> → NIP (guru) atau NISN (siswa)</span>
                    <span><code style="background:var(--bg-card-header); border-radius:4px; padding:0 5px; font-size:11px; color:#ef4444;">password</code> → opsional, default: <code style="font-size:11px;">password123</code></span>
                </div>
            </div>
        </div>

    </div>{{-- end left --}}

    {{-- ── RIGHT: SK Files + Stats ────────────────────────────── --}}
    <div>

        {{-- Card SK Files --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h3 class="card-title">📁 File SK Penugasan 2026/2027</h3>
            </div>
            <div class="card-body" style="padding:12px 16px;">
                <p style="font-size:12px; color:var(--text-muted); margin:0 0 8px;">
                    Unduh data CSV hasil ekstraksi SK Kepala Sekolah:
                </p>
                @foreach($skFiles as $f)
                <div class="file-dl-row">
                    <div class="file-dl-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="file-dl-body">
                        <div class="file-dl-title">{{ $f['title'] }}</div>
                        <div class="file-dl-sub">{{ $f['size'] }} &bull; {{ $f['desc'] }}</div>
                    </div>
                    <a href="{{ asset('csv/' . $f['filename']) }}" download
                       class="btn btn-secondary btn-sm file-dl-btn" title="Unduh">
                        ⬇️
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Card Stats --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📊 Data Tersimpan Saat Ini</h3>
            </div>
            <div class="card-body" style="padding:8px 16px 12px;">
                <div class="stat-row">
                    <span class="stat-row-label">👨‍🏫 Guru Terdaftar</span>
                    <span class="stat-row-val">{{ $totalGuru }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">👨‍🎓 Siswa Terdaftar</span>
                    <span class="stat-row-val">{{ $totalSiswa }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">🏫 Rombongan Belajar</span>
                    <span class="stat-row-val">{{ $totalKelas }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">🎓 Wali Kelas Ditugaskan</span>
                    <span class="stat-row-val" style="color:var(--navy-primary);">{{ $totalWaliKelas }}</span>
                </div>
            </div>
        </div>

        {{-- Quick links --}}
        <div style="margin-top:16px; display:flex; flex-direction:column; gap:8px;">
            <a href="{{ route('guru.index') }}" class="btn btn-secondary" style="justify-content:flex-start; font-size:13px;">
                👨‍🏫 Kelola Data Guru →
            </a>
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary" style="justify-content:flex-start; font-size:13px;">
                👨‍🎓 Kelola Data Siswa →
            </a>
            <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-secondary" style="justify-content:flex-start; font-size:13px;">
                🎓 Manajemen Wali Kelas →
            </a>
        </div>

    </div>{{-- end right --}}

</div>{{-- end csv-layout --}}

<script>
const dropzone = document.getElementById('dropzone');

// Click opens file picker
dropzone?.addEventListener('click', () => {
    document.getElementById('csv_file_input').click();
});

// Drag & Drop
['dragover', 'dragenter'].forEach(evt => {
    dropzone?.addEventListener(evt, e => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
});
['dragleave', 'dragend'].forEach(evt => {
    dropzone?.addEventListener(evt, () => dropzone.classList.remove('dragover'));
});
dropzone?.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const input = document.getElementById('csv_file_input');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        handleCsvPicked(input);
    }
});

function handleCsvPicked(input) {
    if (input.files && input.files[0]) {
        const badge = document.getElementById('fileBadge');
        document.getElementById('fileNameSpan').textContent = input.files[0].name;
        badge.style.display = 'inline-flex';
    }
}

// Loading state on submit
document.getElementById('csvUploadForm')?.addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    if (btn) { btn.textContent = '⏳ Memproses...'; btn.disabled = true; }
});
</script>
@endsection
