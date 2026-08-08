@extends('layouts.app')

@section('content')
<h2>Edit Absensi Siswa</h2>
<a href="{{ route('absensi-siswa.index') }}">&#8592; Kembali</a><br><br>

<p><strong>Siswa:</strong> {{ $absensi->siswa->nama ?? '-' }}</p>
<p><strong>Jurnal:</strong> {{ $absensi->jurnal->tanggal ?? '-' }} - {{ $absensi->jurnal->mapel ?? '-' }}</p>

<form action="{{ route('absensi-siswa.update', $absensi->id_absensi) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>Status *</label></td><td>
            <select name="status" required style="padding:5px;">
                <option value="hadir" {{ $absensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="sakit" {{ $absensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="izin" {{ $absensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="alpa" {{ $absensi->status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                <option value="terlambat" {{ $absensi->status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
        </td></tr>
        <tr><td><label>Keterangan</label></td><td><input type="text" name="keterangan" value="{{ old('keterangan', $absensi->keterangan) }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td></tr>
    </table>
</form>
@endsection