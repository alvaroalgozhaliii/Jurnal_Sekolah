@extends('layouts.app')

@section('content')
<h2>Detail Siswa: {{ $siswa->nama }}</h2>
<a href="{{ route('siswa.index') }}">&#8592; Kembali</a> | <a href="{{ route('siswa.edit', $siswa->id_siswa) }}">Edit</a>
<br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>NIS</th><td>{{ $siswa->nis }}</td></tr>
    <tr><th>Nama</th><td>{{ $siswa->nama }}</td></tr>
    <tr><th>Kelas</th><td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
    <tr><th>Jurusan</th><td>{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
    <tr><th>Jenis Kelamin</th><td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td></tr>
    <tr><th>Tempat Lahir</th><td>{{ $siswa->tempat_lahir ?? '-' }}</td></tr>
    <tr><th>Tanggal Lahir</th><td>{{ $siswa->tanggal_lahir ?? '-' }}</td></tr>
    <tr><th>No Telp Ortu</th><td>{{ $siswa->no_telp_ortu ?? '-' }}</td></tr>
    <tr><th>Status</th><td>{{ $siswa->aktif ? 'Aktif' : 'Tidak Aktif' }}</td></tr>
    <tr><th>Akun User</th><td>{{ $siswa->user->username ?? 'Belum ada akun' }}</td></tr>
</table>
@endsection