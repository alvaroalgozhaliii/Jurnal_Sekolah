@extends('layouts.app')

@section('content')
<h2>Jadwal Pelajaran Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</h2>

@php
    $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp

@if(count($jadwal) > 0)
    @foreach($hariOrder as $hari)
        @if(isset($jadwal[$hari]) && count($jadwal[$hari]) > 0)
            <div class="card mb-16">
                <div class="card-header card-header-navy">
                    <h3 class="card-title" style="color:#fff; margin:0;"> HARI {{ strtoupper($hari) }}</h3>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrapper" style="border:none; border-radius:0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Jam Ke</th>
                                    <th style="width:160px;">Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th style="width:80px;">Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwal[$hari]->sortBy('jam_ke') as $j)
                                    <tr>
                                        <td class="fw-bold text-center">Jam {{ $j->jam_ke }}</td>
                                        <td class="fw-bold text-navy">{{ $j->waktu_mulai ? \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') : '-' }} - {{ $j->waktu_selesai ? \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') : '-' }}</td>
                                        <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                                        <td>{{ $j->guru->nama ?? '-' }}</td>
                                        <td>{{ $j->ruang ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@else
    <p>Belum ada jadwal pelajaran untuk kelas Anda.</p>
@endif
@endsection
