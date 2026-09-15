@extends('layouts.app')

@section('title', 'Data Guru — Jurnal Sekolah')
@section('page-title', 'Data Guru')

@section('content')
<div id="guruPageContent">
<div class="page-header">
    <div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle">Kelola Master Data Guru Sekolah</p>
    </div>
    <div class="page-actions">
        <button type="button" id="btnBulkModeToggle" class="btn btn-secondary btn-bulk-mode-toggle" onclick="toggleBulkMode()" title="Aktifkan mode pilih untuk seleksi data">
            ☑ Mode Pilih
        </button>
        <a href="{{ route('guru.create') }}" class="btn btn-primary">+ Tambah Guru</a>
        <a href="{{ route('guru.export-csv', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
        <a href="{{ route('guru.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- CSV Import Card --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong class="text-navy" style="font-size:14px;">Import Data Guru via CSV:</strong>
                <a href="{{ route('guru.import-template') }}" class="btn btn-secondary btn-sm" style="font-size:12px; padding:4px 10px;">
                    Download Template CSV
                </a>
            </div>
            <form action="{{ route('guru.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required style="font-size:12px;">
                <button type="submit" class="btn btn-primary btn-sm">Upload &amp; Import</button>
            </form>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('guru.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama guru, NIP, bidang studi..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($guru->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table" style="min-width:720px;">
                <thead>
                    <tr>
                        <th class="bulk-check-col">
                            <input type="checkbox" class="bulk-checkbox" id="checkboxSelectAll" title="Pilih Semua">
                        </th>
                        <th class="no-col">No</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Bidang Studi</th>
                        <th>No Telepon</th>
                        <th>Akun User</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guru as $item)
                    <tr>
                        <td class="bulk-check-col">
                            <input type="checkbox" class="bulk-checkbox"
                                data-id="{{ $item->id_guru }}"
                                data-edit-url="{{ route('guru.edit', $item->id_guru) }}"
                                data-show-url="{{ route('guru.show', $item->id_guru) }}">
                        </td>
                        <td class="no-col">{{ $guru->firstItem() + $loop->index }}</td>
                        <td class="fw-bold text-navy">{{ $item->nama }}</td>
                        <td class="text-muted">{{ $item->nip ?? '-' }}</td>
                        <td>{{ $item->bidang_studi ?? '-' }}</td>
                        <td>{{ $item->no_telp ?? '-' }}</td>
                        <td>
                            @if($item->user)
                                <span class="badge badge-success">{{ $item->user->username }}</span>
                            @else
                                <span class="badge badge-gray">Belum ada akun</span>
                            @endif
                        </td>
                        <td class="action-col">
                            <a href="{{ route('guru.show', $item->id_guru) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('guru.edit', $item->id_guru) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('guru.destroy', $item->id_guru) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data guru ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($guru->hasPages())
        <div class="card-footer">
            <div class="text-muted" style="font-size: 13px;">
                Menampilkan <strong>{{ $guru->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $guru->lastItem() ?? 0 }}</strong> dari <strong>{{ $guru->total() }}</strong> guru
            </div>
            <div>
                {{ $guru->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="empty-state" style="padding: 32px; text-align: center;">
            <div class="empty-state-text" style="color: var(--text-muted); font-size: 14px;">Tidak ada data guru ditemukan.</div>
        </div>
        @endif
    </div>
</div>

@include('partials.bulk-action-bar', [
    'bulkDeleteRoute'  => route('guru.bulk-delete'),
    'hasDetail'        => true,
    'hasEdit'          => true,
    'bulkEntityLabel'  => 'guru',
])

@push('scripts')
<script>
function toggleBulkMode() {
    const content = document.getElementById('guruPageContent');
    const btn     = document.getElementById('btnBulkModeToggle');
    const isActive = content.classList.toggle('bulk-mode');
    btn.classList.toggle('active', isActive);
    btn.innerHTML  = isActive ? '✕ Matikan Pilih' : '☑ Mode Pilih';
    if (!isActive) { clearBulkSelection(); }
}
</script>
@endpush
</div>{{-- end #guruPageContent --}}
@endsection