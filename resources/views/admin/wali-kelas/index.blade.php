@extends('layouts.app')

@section('title', 'Data Wali Kelas — Jurnal Sekolah')
@section('page-title', 'Data Wali Kelas')

@section('content')
<style>
.stat-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
}
.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon svg { width: 22px; height: 22px; }
.stat-content { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
.stat-value { font-size: 24px; font-weight: 800; color: var(--text-primary); margin-top: 2px; }

/* Modal Custom */
.custom-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 16px;
}
.custom-modal-backdrop.show { display: flex; }
.custom-modal {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 500px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.custom-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card-header);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.custom-modal-body { padding: 20px; }
.custom-modal-footer {
    padding: 14px 20px;
    border-top: 1px solid var(--border);
    background: var(--bg-card-header);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div>
        <h1 class="page-title">Data Wali Kelas</h1>
        <p class="page-subtitle">Kelola penugasan dan monitoring wali kelas untuk setiap rombongan belajar</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ asset('csv/sk_wali_kelas_2026_2027.csv') }}" class="btn btn-secondary" download>
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Unduh SK Wali Kelas (CSV)
        </a>
        <a href="{{ route('admin.wali-kelas.export') }}" class="btn btn-outline">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export Data (Live)
        </a>
        <a href="{{ route('admin.csv-master.index') }}" class="btn btn-primary">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Import CSV Master
        </a>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-summary-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e0e7ff; color:#3730a3;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Total Rombel Kelas</span>
            <span class="stat-value">{{ $totalKelas }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7; color:#15803d;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Sudah Ada Wali Kelas</span>
            <span class="stat-value">{{ $totalAssigned }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2; color:#b91c1c;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Belum Memiliki Wali</span>
            <span class="stat-value">{{ $totalUnassigned }}</span>
        </div>
    </div>
</div>

{{-- FILTER SECTION --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <form action="{{ route('admin.wali-kelas.index') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
            <div style="flex:1; min-width:180px;">
                <label class="form-label" style="font-size:12px;">Pencarian Kelas / Nama Wali</label>
                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama kelas atau guru...">
            </div>
            <div style="width:130px;">
                <label class="form-label" style="font-size:12px;">Tingkat</label>
                <select name="tingkat" class="form-control">
                    <option value="">Semua</option>
                    <option value="X" {{ $tingkat == 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ $tingkat == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ $tingkat == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>
            <div style="width:180px;">
                <label class="form-label" style="font-size:12px;">Konsentrasi Keahlian</label>
                <select name="id_jurusan" class="form-control">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id_jurusan }}" {{ $idJurusan == $jur->id_jurusan ? 'selected' : '' }}>
                            {{ $jur->kode_jurusan ?? $jur->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="width:160px;">
                <label class="form-label" style="font-size:12px;">Status Penugasan</label>
                <select name="status_wali" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="assigned" {{ $statusWali == 'assigned' ? 'selected' : '' }}>Sudah Ditugaskan</option>
                    <option value="unassigned" {{ $statusWali == 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:40px;">Filter</button>
                <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-secondary" style="height:40px;">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="card">
    <div class="card-body" style="padding:0; overflow-x:auto;">
        <table class="table" style="margin:0;">
            <thead>
                <tr>
                    <th style="width:50px; text-align:center;">No</th>
                    <th style="width:140px;">Kelas / Rombel</th>
                    <th>Konsentrasi Keahlian</th>
                    <th>Wali Kelas</th>
                    <th>Kontak / HP</th>
                    <th style="text-align:center; width:100px;">Jml Siswa</th>
                    <th style="text-align:center; width:130px;">Status</th>
                    <th style="text-align:center; width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelasList as $index => $k)
                    <tr>
                        <td style="text-align:center;">{{ $index + 1 }}</td>
                        <td>
                            <strong style="color:var(--navy-primary); font-size:15px;">{{ $k->nama_kelas }}</strong>
                            <div style="font-size:11px; color:var(--text-muted);">Tingkat {{ $k->tingkat }}</div>
                        </td>
                        <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>
                            @if($k->guruWaliKelas)
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:34px; height:34px; border-radius:50%; background:#1e3a8a; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0;">
                                        {{ strtoupper(substr($k->guruWaliKelas->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:var(--text-primary);">{{ $k->guruWaliKelas->nama }}</div>
                                        <small style="color:var(--text-muted); font-size:11.5px;">NIP: {{ $k->guruWaliKelas->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            @elseif($k->wali_kelas)
                                <div style="font-weight:600; color:var(--text-primary);">{{ $k->wali_kelas }}</div>
                                <small style="color:var(--text-muted); font-size:11px;">(Nama manual tertera)</small>
                            @else
                                <span style="color:#ef4444; font-weight:500; font-style:italic;">Belum Ditentukan</span>
                            @endif
                        </td>
                        <td>
                            @if($k->guruWaliKelas && $k->guruWaliKelas->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $k->guruWaliKelas->no_telp) }}" target="_blank" style="color:#16a34a; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                    <span>📱</span> {{ $k->guruWaliKelas->no_telp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-navy" style="font-size:12px;">{{ $k->siswa->count() }} siswa</span>
                        </td>
                        <td style="text-align:center;">
                            @if($k->id_guru_walikelas)
                                <span class="badge badge-success">● Ditugaskan</span>
                            @else
                                <span class="badge badge-danger">● Belum Ada</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-primary" 
                                    onclick="openAssignModal({{ $k->id_kelas }}, '{{ addslashes($k->nama_kelas) }}', '{{ $k->id_guru_walikelas ?? '' }}')">
                                ✏️ Atur Wali
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-muted);">
                            Tidak ada data kelas yang cocok dengan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TETAPKAN WALI KELAS --}}
<div id="assignWaliModal" class="custom-modal-backdrop">
    <div class="custom-modal">
        <form id="assignWaliForm" action="" method="POST">
            @csrf
            <div class="custom-modal-header">
                <h3 style="margin:0; font-size:16px; font-weight:700;">Penetapan Wali Kelas</h3>
                <button type="button" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);" onclick="closeAssignModal()">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p style="margin-top:0; margin-bottom:14px; color:var(--text-secondary); font-size:13.5px;">
                    Tetapkan guru pendamping / wali kelas untuk rombongan belajar <strong id="modalKelasName" style="color:var(--navy-primary);">-</strong>:
                </p>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="selectGuruWali">Pilih Guru Wali Kelas <span class="req">*</span></label>
                    <select id="selectGuruWali" name="id_guru" class="form-control select-search" required>
                        <option value="">-- Pilih Guru / Kosongkan --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id_guru }}">
                                {{ $guru->nama }} (NIP: {{ $guru->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="display:block; margin-top:6px;">
                        Jika guru sebelumnya telah menjadi wali di kelas lain, penugasannya akan otomatis dialihkan ke kelas ini.
                    </small>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Batal</button>
                <button type="button" class="btn btn-outline" onclick="kosongkanWali()">Hapus Penugasan</button>
                <button type="submit" class="btn btn-primary">Simpan Wali Kelas</button>
            </div>
        </form>
    </div>
</div>

<script>
let tomSelectWali = null;

document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('selectGuruWali');
    if (el && window.TomSelect) {
        tomSelectWali = new TomSelect(el, {
            create: false,
            placeholder: 'Ketik untuk mencari nama guru...',
            allowEmptyOption: true
        });
    }
});

function openAssignModal(idKelas, namaKelas, idGuruCurrent) {
    const modal = document.getElementById('assignWaliModal');
    const form = document.getElementById('assignWaliForm');
    const nameSpan = document.getElementById('modalKelasName');

    form.action = "{{ url('admin/wali-kelas') }}/" + idKelas;
    nameSpan.textContent = namaKelas;

    if (tomSelectWali) {
        tomSelectWali.setValue(idGuruCurrent || '');
    } else {
        document.getElementById('selectGuruWali').value = idGuruCurrent || '';
    }

    modal.classList.add('show');
}

function closeAssignModal() {
    document.getElementById('assignWaliModal').classList.remove('show');
}

function kosongkanWali() {
    if (confirm('Yakin ingin mengosongkan wali kelas untuk rombel ini?')) {
        if (tomSelectWali) {
            tomSelectWali.setValue('');
        } else {
            document.getElementById('selectGuruWali').value = '';
        }
        document.getElementById('assignWaliForm').submit();
    }
}
</script>
@endsection
