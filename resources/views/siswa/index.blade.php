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

{{-- Filter Jurusan --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">
            Filter Jurusan
        </div>
        @php
            $warna = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#84cc16','#6366f1'];
        @endphp
        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
            <a href="{{ route('siswa.index', array_merge(request()->except(['jurusan','page']), [])) }}"
               class="btn btn-sm {{ !$jurusanId ? 'btn-primary' : 'btn-secondary' }}">
                Semua
            </a>
            @foreach($semuaJurusan as $idx => $j)
            @php $wn = $warna[$idx % count($warna)]; $isActive = $jurusanId == $j->id_jurusan; @endphp
            <a href="{{ route('siswa.index', array_merge(request()->except(['jurusan','page']), ['jurusan' => $j->id_jurusan])) }}"
               class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-secondary' }}"
               style="color: {{ $isActive ? '#fff' : $wn }} !important;">
                {{ $j->nama_jurusan }}
            </a>
            @endforeach
        </div>
    </div>
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
