@extends('layouts.app')

@section('title', 'Data Siswa — Jurnal Sekolah')
@section('page-title', 'Data Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola Master Data Siswa & Akun Orang Tua</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">+ Tambah Siswa</a>
        <a href="{{ route('siswa.trash') }}" class="btn btn-secondary">Lihat Trash</a>
    </div>
</div>

{{-- CSV Import Card --}}
<div class="card mb-16" style="background:#f8fafc; border:1px dashed #cbd5e1;">
    <div class="card-body" style="padding:12px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <strong style="color:var(--text-navy, #1e293b); font-size:14px;">&#x1F4E5; Import Data Siswa via CSV:</strong>
                <a href="{{ route('siswa.import-template') }}" class="btn btn-secondary btn-sm" style="font-size:12px; padding:4px 10px;">
                    &#x2B07; Download Template CSV
                </a>
            </div>
            <form action="{{ route('siswa.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required style="font-size:12px;">
                <button type="submit" class="btn btn-primary btn-sm">&#x1F4E4; Upload &amp; Import</button>
            </form>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="card mb-16">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('siswa.index') }}" class="d-flex gap-8" style="align-items:center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama siswa, NISN, kelas..." class="form-control" style="max-width:420px;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if($search ?? false)
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
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
                        <td class="no-col">{{ $loop->iteration }}</td>
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
    </div>
</div>
@endsection
