@extends('layouts.app')

@section('title', 'Backup & Restore — Jurnal Sekolah')
@section('page-title', 'Backup & Restore Database')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Backup & Restore Database</h1>
        <p class="page-subtitle">Pemeliharaan & Cadangan Data Sistem Sekolah</p>
    </div>
</div>

<div class="grid-2">
    <!-- BACKUP DATABASE CARD -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">1. Backup Database</h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Unduh file cadangan database sistem dalam format JSON secara aman.</p>
            <a href="{{ route('admin.backup.download') }}" class="btn btn-primary btn-lg">
                Unduh Backup Database (.JSON)
            </a>
        </div>
    </div>

    <!-- RESTORE DATABASE CARD -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">2. Restore Database</h3>
        </div>
        <div class="card-body">
            <p class="mb-16 text-muted">Unggah file backup JSON untuk memulihkan seluruh data sistem.</p>
            
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="json_file">Pilih File Backup (.json)</label>
                    <input type="file" id="json_file" name="json_file" accept=".json" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-danger btn-lg mt-16" onclick="return confirm('PERINGATAN: Memulihkan database akan menimpa data yang ada. Lanjutkan?')">
                    Restore Database Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
