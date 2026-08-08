@extends('layouts.app')

@section('content')
<h2>Detail Jurusan: {{ $jurusan->nama_jurusan }}</h2>
<a href="{{ route('jurusan.index') }}">&#8592; Kembali</a> | <a href="{{ route('jurusan.edit', $jurusan->id_jurusan) }}">Edit</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Nama Jurusan</th><td>{{ $jurusan->nama_jurusan }}</td></tr>
    <tr><th>Kode Rombel</th><td>{{ $jurusan->rombel }}</td></tr>
    <tr><th>Maks Rombel</th><td>{{ $jurusan->maks_rombel }}</td></tr>
</table>

<h3>Daftar Kelas ({{ $jurusan->kelas->count() }} kelas)</h3>
@if($jurusan->kelas->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>No</th><th>Nama Kelas</th><th>Tingkat</th><th>Wali Kelas</th></tr>
    @foreach($jurusan->kelas as $k)
    <tr><td>{{ $loop->iteration }}</td><td>{{ $k->nama_kelas }}</td><td>{{ $k->tingkat }}</td><td>{{ $k->wali_kelas ?? '-' }}</td></tr>
    @endforeach
</table>
@else
<p>Belum ada kelas di jurusan ini.</p>
@endif
@endsection
