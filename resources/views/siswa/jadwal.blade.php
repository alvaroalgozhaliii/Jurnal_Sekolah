@extends('layouts.app')

@section('content')
<h2>Jadwal Pelajaran Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</h2>

@php
    $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp

@if(count($jadwal) > 0)
    @foreach($hariOrder as $hari)
        @if(isset($jadwal[$hari]) && count($jadwal[$hari]) > 0)
            <h3 style="color:#0066cc; margin-top:15px; margin-bottom:5px;">Hari: {{ $hari }}</h3>
            <table border="1" cellpadding="8" style="border-collapse:collapse; width:100%; font-size:13px; margin-bottom:15px;">
                <thead>
                    <tr>
                        <th>Jam Ke</th>
                        <th>Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal[$hari]->sortBy('jam_ke') as $j)
                        <tr>
                            <td>Jam ke-{{ $j->jam_ke }}</td>
                            <td>{{ $j->waktu_mulai ? \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') : '-' }} - {{ $j->waktu_selesai ? \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') : '-' }}</td>
                            <td><strong>{{ $j->mapel }}</strong></td>
                            <td>{{ $j->guru->nama ?? '-' }}</td>
                            <td>{{ $j->ruang ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
@else
    <p>Belum ada jadwal pelajaran untuk kelas Anda.</p>
@endif
@endsection
