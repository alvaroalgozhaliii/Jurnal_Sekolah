@extends('layouts.app')

@section('content')
<h2>Tahun Pelajaran</h2>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px; max-width:500px;">
<h3>Tambah Tahun Pelajaran</h3>
<form action="{{ route('tahun-pelajaran.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Tahun Pelajaran *</label></td><td><input type="text" name="tahun" placeholder="Contoh: 2025/2026" required style="width:200px; padding:5px;"></td></tr>
        <tr><td><label>Semester *</label></td><td>
            <select name="semester" required style="padding:5px;">
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </td></tr>
        <tr><td><label>Jadikan Aktif</label></td><td><input type="checkbox" name="aktif" value="1"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
</div>

<h3>Daftar Tahun Pelajaran</h3>
@if($tahun->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse; max-width:600px;">
    <thead><tr><th>No</th><th>Tahun</th><th>Semester</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($tahun as $tp)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $tp->tahun }}</td>
        <td>{{ $tp->semester }}</td>
        <td>{{ $tp->aktif ? '✅ AKTIF' : 'Tidak Aktif' }}</td>
        <td>
            @if(!$tp->aktif)
            <form action="{{ route('tahun-pelajaran.set-aktif', $tp->id_tahun_pelajaran) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="color:green;">Aktifkan</button>
            </form>
            | 
            @endif
            <form action="{{ route('tahun-pelajaran.destroy', $tp->id_tahun_pelajaran) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus tahun pelajaran ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Belum ada data tahun pelajaran.</p>
@endif
@endsection
