@extends('layouts.app')

@section('content')
<h2>Dashboard Piket</h2>

@if(!empty($kelasKosong))
    <div class="alert-error" style="border: 2px solid red;">
        <h4>🚨 PERINGATAN KELAS KOSONG:</h4>
        <ul>
            @foreach($kelasKosong as $kk)
                <li>
                    <strong>{{ $kk['pesan'] }}</strong> 
                    (Ruang: {{ $kk['jadwal']->ruang ?? '-' }}, Guru: {{ $kk['jadwal']->guru->nama ?? 'Belum ditentukan' }})
                </li>
            @endforeach
        </ul>
    </div>
@endif

<h3>Ringkasan Presensi Hari Ini</h3>
<table>
    <tr>
        <th>Guru Hadir</th>
        <th>Siswa Hadir</th>
        <th>Siswa Tidak Hadir (Izin/Sakit/Alpa)</th>
    </tr>
    <tr>
        <td><strong>{{ $jumlahGuruHadir }}</strong> guru</td>
        <td><strong>{{ $jumlahSiswaHadir }}</strong> siswa</td>
        <td><strong>{{ $jumlahSiswaTidakHadir }}</strong> siswa</td>
    </tr>
</table>

<br>
<h3>Jadwal Pelajaran Hari Ini</h3>
@if($jadwalHariIni->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Jam Ke</th>
                <th>Waktu</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Ruang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalHariIni as $j)
                <tr>
                    <td>{{ $j->jam_ke }}</td>
                    <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel }}</td>
                    <td>{{ $j->guru->nama ?? '-' }}</td>
                    <td>{{ $j->ruang ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada jadwal untuk hari ini.</p>
@endif

<br>
<h3>Guru yang Belum Hadir (Presensi Masuk)</h3>
@if($guruBelumHadir->count() > 0)
    <ul>
        @foreach($guruBelumHadir as $gb)
            <li>{{ $gb->nama }} (NIP: {{ $gb->nip ?? '-' }})</li>
        @endforeach
    </ul>
@else
    <p>Semua guru sudah tercatat hadir.</p>
@endif
@endsection
