@extends('layouts.app')

@section('title', 'Backup & Restore — Jurnal Sekolah')
@section('page-title', 'Backup & Restore Database')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Backup & Restore Database</h1>
        <p class="page-subtitle">Pemeliharaan, Pencadangan, & Pemulihan Data Sistem Sekolah</p>
    </div>
</div>

<div class="grid-2 mb-24">
    <!-- BACKUP DATABASE CARD -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">1. Buat Backup Database</h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Ekspor seluruh skema dan data database ke file SQL standar. File ini juga otomatis tersimpan di server.</p>
            <a href="{{ route('admin.backup.export') }}" class="btn btn-primary btn-lg">
                Ekspor Database Sekarang (.SQL)
            </a>
        </div>
    </div>

    <!-- RESTORE DATABASE CARD -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">2. Restore dari File</h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Unggah file backup <code>.sql</code> atau <code>.json</code> untuk memulihkan seluruh data sistem.</p>
            
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="backup_file">Pilih File Backup (.sql / .json)</label>
                    <input type="file" id="backup_file" name="backup_file" accept=".sql,.json" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-danger mt-16" onclick="return confirm('PERINGATAN: Memulihkan database akan menimpa data yang ada saat ini. Pastikan Anda sudah membuat backup terlebih dahulu. Lanjutkan?')">
                    Restore Database Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<!-- STORED BACKUPS TABLE -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">File Cadangan Tersimpan di Server ({{ count($backupFiles ?? []) }})</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if(!empty($backupFiles) && count($backupFiles) > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th>Nama File Cadangan</th>
                        <th>Ukuran File</th>
                        <th>Tanggal Dibuat</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backupFiles as $bf)
                    <tr>
                        <td class="no-col">{{ $loop->iteration }}</td>
                        <td><span class="fw-bold text-navy" style="font-family:monospace;font-size:13px;">{{ $bf['name'] }}</span></td>
                        <td>{{ number_format($bf['size'] / 1024, 1) }} KB</td>
                        <td class="text-muted">{{ $bf['modified'] }}</td>
                        <td class="action-col" style="white-space:nowrap;">
                            <a href="{{ route('admin.backup.download', $bf['name']) }}" class="btn btn-secondary btn-sm" title="Unduh File">
                                Unduh
                            </a>
                            <form action="{{ route('admin.backup.restore-stored', $bf['name']) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Pulihkan database dari file {{ $bf['name'] }}? Data saat ini akan diperbarui.')">
                                    Restore
                                </button>
                            </form>
                            <form action="{{ route('admin.backup.delete', $bf['name']) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus file backup {{ $bf['name'] }}?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada file cadangan yang tersimpan di server. Klik "Ekspor Database Sekarang" untuk membuat cadangan pertama.</div>
        </div>
        @endif
    </div>
</div>
@endsection
