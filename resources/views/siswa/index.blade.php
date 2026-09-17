@extends('layouts.app')

@section('title', 'Data Siswa — Jurnal Sekolah')
@section('page-title', 'Data Siswa')

@section('content')
<div id="siswaPageContent">
<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola Master Data Siswa &amp; Akun Orang Tua</p>
    </div>
    <div class="page-actions">
        <button type="button" id="btnBulkModeToggle" class="btn btn-secondary btn-bulk-mode-toggle" onclick="toggleBulkMode()" title="Aktifkan mode pilih untuk seleksi data">
            ☑ Mode Pilih
        </button>
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">+ Tambah Siswa</a>
        <a href="{{ route('siswa.export-csv', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
        <a href="{{ route('siswa.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- CSV Import Card --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong class="text-navy" style="font-size:14px;">Import Data Siswa via CSV:</strong>
                <a href="{{ route('siswa.import-template') }}" class="btn btn-secondary btn-sm" style="font-size:12px; padding:4px 10px;">
                    Download Template CSV
                </a>
            </div>
            <form action="{{ route('siswa.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required style="font-size:12px;">
                <button type="submit" class="btn btn-primary btn-sm">Upload &amp; Import</button>
            </form>
        </div>
    </div>
</div>

{{-- Filter Jurusan (Desain Identik dengan Filter Role Pengguna) --}}
<style>
.jb {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .3px;
    line-height: 1.2;
    border: 1px solid transparent;
}
.jb-red     { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
.jb-blue    { background:#dbeafe; color:#1e40af; border-color:#bfdbfe; }
.jb-green   { background:#dcfce7; color:#166534; border-color:#bbf7d0; }
.jb-purple  { background:#e0e7ff; color:#3730a3; border-color:#c7d2fe; }
.jb-amber   { background:#fef3c7; color:#92400e; border-color:#fde68a; }
.jb-orange  { background:#ffedd5; color:#9a3412; border-color:#fed7aa; }
.jb-cyan    { background:#cffafe; color:#155e75; border-color:#a5f3fc; }
.jb-pink    { background:#fce7f3; color:#9d174d; border-color:#fbcfe8; }
.jb-teal    { background:#ccfbf1; color:#115e59; border-color:#99f6e4; }
.jb-indigo  { background:#ede9fe; color:#5b21b6; border-color:#ddd6fe; }

[data-theme="dark"] .jb-red    { background: rgba(239, 68, 68, 0.16); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
[data-theme="dark"] .jb-blue   { background: rgba(59, 130, 246, 0.16); color: #93c5fd; border-color: rgba(59, 130, 246, 0.3); }
[data-theme="dark"] .jb-green  { background: rgba(34, 197, 94, 0.16); color: #86efac; border-color: rgba(34, 197, 94, 0.3); }
[data-theme="dark"] .jb-purple { background: rgba(99, 102, 241, 0.16); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3); }
[data-theme="dark"] .jb-amber  { background: rgba(245, 158, 11, 0.16); color: #fde047; border-color: rgba(245, 158, 11, 0.3); }
[data-theme="dark"] .jb-orange { background: rgba(249, 115, 22, 0.16); color: #fdba74; border-color: rgba(249, 115, 22, 0.3); }
[data-theme="dark"] .jb-cyan   { background: rgba(6, 182, 212, 0.16); color: #67e8f9; border-color: rgba(6, 182, 212, 0.3); }
[data-theme="dark"] .jb-pink   { background: rgba(236, 72, 153, 0.16); color: #f472b6; border-color: rgba(236, 72, 153, 0.3); }
[data-theme="dark"] .jb-teal   { background: rgba(20, 184, 166, 0.16); color: #5eead4; border-color: rgba(20, 184, 166, 0.3); }
[data-theme="dark"] .jb-indigo { background: rgba(139, 92, 246, 0.16); color: #c4b5fd; border-color: rgba(139, 92, 246, 0.3); }

.role-filter-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 10px;
    margin-bottom: 20px;
    align-items: center;
}
.role-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    text-decoration: none;
}
.role-filter-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.role-filter-btn.active {
    background: var(--navy-primary, #1e3a8a);
    border-color: var(--navy-primary, #1e3a8a);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.25);
}
.role-filter-btn.active .jb {
    border-color: rgba(255, 255, 255, 0.3);
}
.role-filter-btn.active .role-count-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.role-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 4px;
    background: #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
}

[data-theme="dark"] .role-filter-btn {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
[data-theme="dark"] .role-filter-btn:hover {
    background: #28354d;
    border-color: #475569;
}
[data-theme="dark"] .role-filter-btn.active {
    background: #2563eb;
    border-color: #3b82f6;
    color: #ffffff !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.4);
}
[data-theme="dark"] .role-count-badge {
    background: #0f172a;
    color: #94a3b8;
}
</style>

@php
    $badgeClasses = ['jb-red', 'jb-blue', 'jb-green', 'jb-purple', 'jb-amber', 'jb-orange', 'jb-cyan', 'jb-pink', 'jb-teal', 'jb-indigo'];
@endphp

<div class="role-filter-container">
    @foreach($semuaJurusan as $idx => $j)
    @php
        $isActive = $jurusanId == $j->id_jurusan;
        $bClass = $badgeClasses[$idx % count($badgeClasses)];
        $count = $jurusanCounts[$j->id_jurusan] ?? 0;
    @endphp
    <a href="{{ route('siswa.index', array_merge(request()->except(['jurusan','page']), ['jurusan' => $j->id_jurusan])) }}"
       class="role-filter-btn {{ $isActive ? 'active' : '' }}"
       id="filterBtn_{{ $j->id_jurusan }}"
       style="text-decoration: none;">
        <span class="jb {{ $bClass }}">{{ $j->nama_jurusan }}</span>
        <span class="role-count-badge">{{ $count }}</span>
    </a>
    @endforeach

    <a href="{{ route('siswa.index', array_merge(request()->except(['jurusan','page']), [])) }}"
       class="role-filter-btn {{ empty($jurusanId) ? 'active' : '' }}"
       id="filterBtnAll"
       style="text-decoration: none;">
        <span>Semua</span>
        <span class="role-count-badge">{{ $totalSiswa ?? 0 }}</span>
    </a>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('siswa.index') }}" class="d-flex gap-8" style="align-items:center;">
            @if($jurusanId)
                <input type="hidden" name="jurusan" value="{{ $jurusanId }}">
            @endif
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama siswa, NISN, kelas..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('siswa.index', $jurusanId ? ['jurusan' => $jurusanId] : []) }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>


<div class="card">
    <div class="card-body" style="padding:0;">
        @if($siswa->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table" style="min-width:720px;">
                <thead>
                    <tr>
                        <th class="bulk-check-col">
                            <input type="checkbox" class="bulk-checkbox" id="checkboxSelectAll" title="Pilih Semua">
                        </th>
                        <th class="no-col">No</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $item)
                    <tr>
                        <td class="bulk-check-col">
                            <input type="checkbox" class="bulk-checkbox"
                                data-id="{{ $item->id_siswa }}"
                                data-edit-url="{{ route('siswa.edit', $item->id_siswa) }}"
                                data-show-url="{{ route('siswa.show', $item->id_siswa) }}">
                        </td>
                        <td class="no-col">{{ $siswa->firstItem() + $loop->index }}</td>
                        <td class="text-muted fw-bold">{{ $item->NISN }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td><span class="badge badge-navy">{{ $item->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                        <td>
                            @if($item->aktif)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('siswa.show', $item->id_siswa) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('siswa.edit', $item->id_siswa) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('siswa.destroy', $item->id_siswa) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data siswa ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())
        <div class="card-footer">
            <div class="text-muted" style="font-size: 13px;">
                Menampilkan <strong>{{ $siswa->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $siswa->lastItem() ?? 0 }}</strong> dari <strong>{{ $siswa->total() }}</strong> siswa
            </div>
            <div>
                {{ $siswa->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="empty-state" style="padding: 32px; text-align: center;">
            <div class="empty-state-text" style="color: var(--text-muted); font-size: 14px;">Tidak ada data siswa ditemukan.</div>
        </div>
        @endif
    </div>
</div>

@include('partials.bulk-action-bar', [
    'bulkDeleteRoute'  => route('siswa.bulk-delete'),
    'hasDetail'        => true,
    'hasEdit'          => true,
    'bulkEntityLabel'  => 'siswa',
])

@push('scripts')
<script>
function toggleBulkMode() {
    const content = document.getElementById('siswaPageContent');
    const btn     = document.getElementById('btnBulkModeToggle');
    const isActive = content.classList.toggle('bulk-mode');
    btn.classList.toggle('active', isActive);
    btn.innerHTML  = isActive ? '✕ Matikan Pilih' : '☑ Mode Pilih';
    if (!isActive) { clearBulkSelection(); }
}
</script>
@endpush
</div>{{-- end #siswaPageContent --}}
@endsection
