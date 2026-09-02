@extends('layouts.app')

@section('content')
<h2>Riwayat Presensi Saya</h2>
<p>Nama: <strong>{{ $siswa->nama }}</strong> | NISN: <strong>{{ $siswa->NISN }}</strong></p>

@if($riwayatPresensi->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatPresensi as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->jurnal->tanggal ?? '-' }}</td>
                    <td>{{ $r->jurnal->mapel ?? '-' }}</td>
                    <td>{{ $r->jurnal->guru->nama ?? '-' }}</td>
                    <td><strong>{{ strtoupper($r->status) }}</strong></td>
                    <td>{{ $r->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Belum ada riwayat presensi.</p>
@endif
@endsection
