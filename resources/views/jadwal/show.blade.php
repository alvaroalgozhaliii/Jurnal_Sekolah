@extends('layouts.app')

@section('content')
<h2>Detail Jadwal</h2>
<a href="{{ route('jadwal.index') }}">&#8592; Kembali</a> | <a href="{{ route('jadwal.edit', $jadwal->id_jadwal) }}">Edit</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Hari</th><td>{{ $jadwal->hari }}</td></tr>
    <tr><th>Jam Ke</th><td>{{ $jadwal->jam_ke }}</td></tr>
    <tr><th>Waktu</th><td>{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</td></tr>
    <tr><th>Kelas</th><td>{{ $jadwal->kelas->nama_kelas ?? '-' }}</td></tr>
    <tr><th>Guru</th><td>{{ $jadwal->guru->nama ?? '-' }}</td></tr>
    <tr><th>Mata Pelajaran</th><td>{{ $jadwal->mapel }}</td></tr>
    <tr><th>Ruang</th><td>{{ $jadwal->ruang ?? '-' }}</td></tr>
    <tr><th>Status</th><td>{{ $jadwal->aktif ? 'Aktif' : 'Nonaktif' }}</td></tr>
</table>
@endsection