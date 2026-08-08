@extends('layouts.app')

@section('content')
<h2>Detail Guru: {{ $guru->nama }}</h2>
<a href="{{ route('guru.index') }}">&#8592; Kembali</a> | <a href="{{ route('guru.edit', $guru->id_guru) }}">Edit</a>
<br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Nama</th><td>{{ $guru->nama }}</td></tr>
    <tr><th>NIP</th><td>{{ $guru->nip ?? '-' }}</td></tr>
    <tr><th>Bidang Studi</th><td>{{ $guru->bidang_studi ?? '-' }}</td></tr>
    <tr><th>No Telepon</th><td>{{ $guru->no_telp ?? '-' }}</td></tr>
    <tr><th>Akun User</th><td>{{ $guru->user->username ?? 'Belum ada akun' }}</td></tr>
    <tr><th>Dibuat</th><td>{{ $guru->created_at }}</td></tr>
</table>

<h3>Jadwal Mengajar</h3>
@if($guru->jadwal && $guru->jadwal->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Hari</th><th>Jam Ke</th><th>Waktu</th><th>Kelas</th><th>Mapel</th><th>Ruang</th></tr>
    @foreach($guru->jadwal as $j)
    <tr>
        <td>{{ $j->hari }}</td>
        <td>{{ $j->jam_ke }}</td>
        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
        <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $j->mapel }}</td>
        <td>{{ $j->ruang ?? '-' }}</td>
    </tr>
    @endforeach
</table>
@else
<p>Belum ada jadwal mengajar.</p>
@endif
@endsection