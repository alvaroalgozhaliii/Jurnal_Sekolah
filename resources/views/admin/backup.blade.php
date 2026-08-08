@extends('layouts.app')

@section('content')
<h2>Backup & Restore Database</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
    <h3>1. Backup Database</h3>
    <p>Unduh data cadangan seluruh tabel sistem Jurnal_Sekolah dalam format JSON yang aman.</p>
    <a href="{{ route('admin.backup.export') }}">
        <button style="background-color: green; color: white; padding: 10px 15px;">UNDUH BACKUP DATABASE</button>
    </a>
</div>

<div style="border: 1px solid #ccc; padding: 15px;">
    <h3>2. Restore Database</h3>
    <p>Pilih file backup (format <code>.json</code>) untuk memulihkan data database.</p>
    
    <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="backup_file">File Backup JSON:</label><br>
        <input type="file" id="backup_file" name="backup_file" accept=".json" required><br><br>
        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memulihkan database? Data akan digabungkan/diperbarui secara aman.')" style="background-color: orange; color: black; padding: 8px 15px;">RESTORE DATABASE</button>
    </form>
</div>
@endsection
