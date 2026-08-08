@extends('layouts.app')

@section('content')
<h2>Informasi Kelas / Ruangan</h2>

@if($kelas)
    <table>
        <tr>
            <th style="width: 30%;">Nama Kelas</th>
            <td>{{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <th>Tingkat</th>
            <td>{{ $kelas->tingkat }}</td>
        </tr>
        <tr>
            <th>Jurusan</th>
            <td>{{ $kelas->jurusan->nama_jurusan ?? '-' }} (Kode: {{ $kelas->jurusan->rombel ?? '-' }})</td>
        </tr>
        <tr>
            <th>Wali Kelas</th>
            <td>{{ $kelas->wali_kelas ?? 'Belum ditentukan' }}</td>
        </tr>
    </table>
@else
    <p>Informasi kelas tidak ditemukan.</p>
@endif
@endsection
