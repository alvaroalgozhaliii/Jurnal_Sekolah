@extends('layouts.app')

@section('content')
<h2>Detail Absensi Siswa</h2>
<a href="{{ route('absensi-siswa.index') }}">&#8592; Kembali</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Siswa</th><td>{{ $absensi->siswa->nama ?? '-' }}</td></tr>
    <tr><th>NIS</th><td>{{ $absensi->siswa->nis ?? '-' }}</td></tr>
    <tr><th>Kelas</th><td>{{ $absensi->siswa->kelas->nama_kelas ?? '-' }}</td></tr>
    <tr><th>Tanggal Jurnal</th><td>{{ $absensi->jurnal->tanggal ?? '-' }}</td></tr>
    <tr><th>Mata Pelajaran</th><td>{{ $absensi->jurnal->mapel ?? '-' }}</td></tr>
    <tr><th>Status</th><td><strong>{{ strtoupper($absensi->status) }}</strong></td></tr>
    <tr><th>Keterangan</th><td>{{ $absensi->keterangan ?? '-' }}</td></tr>
    <tr><th>Dicatat Pada</th><td>{{ $absensi->created_at }}</td></tr>
</table>
@endsection