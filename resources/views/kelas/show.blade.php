@extends('layouts.app')

@section('content')
<h2>Detail Kelas: {{ $kelas->nama_kelas }}</h2>
<a href="{{ route('kelas.index') }}">&#8592; Kembali</a> | <a href="{{ route('kelas.edit', $kelas->id_kelas) }}">Edit</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Nama Kelas</th><td>{{ $kelas->nama_kelas }}</td></tr>
    <tr><th>Tingkat</th><td>{{ $kelas->tingkat }}</td></tr>
    <tr><th>Jurusan</th><td>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
    <tr><th>Wali Kelas</th><td>{{ $kelas->wali_kelas ?? '-' }}</td></tr>
</table>

<h3>Daftar Siswa ({{ $kelas->siswa->count() }} siswa)</h3>
@if($kelas->siswa->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>No</th><th>NIS</th><th>Nama</th><th>JK</th></tr>
    @foreach($kelas->siswa as $s)
    <tr><td>{{ $loop->iteration }}</td><td>{{ $s->nis }}</td><td>{{ $s->nama }}</td><td>{{ $s->jenis_kelamin ?? '-' }}</td></tr>
    @endforeach
</table>
@else
<p>Belum ada siswa di kelas ini.</p>
@endif
@endsection