@extends('layouts.app')

@section('title', 'Jadwal Pelajaran — Jurnal Sekolah')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Pelajaran Anak</h1>
        <p class="page-subtitle">Jadwal Pelajaran Mingguan Berdasarkan Kelas</p>
    </div>
</div>

@if($anakList->count() > 1)
<div class="card mb-24">
    <div class="card-body">
        <form action="{{ route('ortu.jadwal-anak') }}" method="GET" class="d-flex align-center gap-12">
            <label class="form-label" style="margin:0; white-space:nowrap;">Pilih Anak:</label>
            <select name="id_siswa" onchange="this.form.submit()" class="form-control" style="max-width:350px;">
                @foreach($anakList as $a)
                <option value="{{ $a->id_siswa }}" {{ ($selectedSiswa && $selectedSiswa->id_siswa == $a->id_siswa) ? 'selected' : '' }}>
                    {{ $a->nama }} (NISN: {{ $a->NISN }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif

@if($selectedSiswa)
<div class="alert alert-info mb-16">
    <div>Anak: <strong>{{ $selectedSiswa->nama }}</strong> | Kelas: <strong>{{ $selectedSiswa->kelas->nama_kelas ?? '-' }}</strong></div>
</div>

@if($jadwal->count() > 0)
    @php
    $urutan = ['Senin','Selasa','Rabu','Kamis','Jumat'];
    $jadwalGrouped = $jadwal->sortBy('jam_ke')->groupBy('hari');
    $istirahatSeninKamis = [4 => 'Istirahat 1 (09:40 - 10:00)', 7 => 'Istirahat 2 (11:45 - 13:15)'];
    $istirahatJumat      = [4 => 'Istirahat 1 (09:00 - 09:30)', 8 => 'Istirahat 2 (11:20 - 13:00)'];
    @endphp
    @foreach($urutan as $hari)
        @if(isset($jadwalGrouped[$hari]))
        @php
        $jadwalHari = $jadwalGrouped[$hari]->sortBy('jam_ke');
        $mapIstirahat = ($hari === 'Jumat') ? $istirahatJumat : $istirahatSeninKamis;
        @endphp
        <div class="card mb-16">
            <div class="card-header" style="background:#1e3a8a;">
                <h3 class="card-title" style="color:#fff; margin:0;"> {{ strtoupper($hari) }}</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrapper" style="border:none; border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:80px;">Jam Ke</th>
                                <th style="width:150px;">Jam Pembelajaran</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th style="width:70px;">Ruang</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($jadwalHari as $j)
                        <tr>
                            <td class="fw-bold text-center">Jam {{ $j->jam_ke }}</td>
                            <td class="fw-bold" style="color:#1e3a8a;">
                                {{ \App\Services\KbmService::getLabelWaktu($j->hari, $j->jam_ke) ?: ($j->waktu_mulai . ' - ' . $j->waktu_selesai) }}
                            </td>
                            <td class="fw-bold text-navy">{{ $j->mapel }}</td>
                            <td>{{ $j->guru->nama ?? '-' }}</td>
                            <td>{{ $j->ruang ?? '-' }}</td>
                        </tr>
                        @if(isset($mapIstirahat[$j->jam_ke]))
                        <tr style="background:#fff7ed;">
                            <td colspan="5" style="text-align:center; font-style:italic; color:#92400e; padding:6px; font-size:12px;">
                                 {{ $mapIstirahat[$j->jam_ke] }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@else
    <div class="empty-state">
        <div class="empty-state-text">Belum ada jadwal pelajaran untuk kelas ini.</div>
    </div>
@endif
@endif
@endsection
