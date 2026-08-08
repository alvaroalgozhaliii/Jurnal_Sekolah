@extends('layouts.app')

@section('content')
<h2>Dashboard Guru</h2>

<p>Selamat datang, <strong>{{ $guru->nama ?? Auth::user()->nama }}</strong></p>

<!-- Pengingat Jurnal & Presensi -->
@if(!empty($pengingatJurnal))
    <div class="alert-error" style="border: 2px solid orange;">
        <h4>⚠️ Pengingat Jurnal Harian:</h4>
        <ul>
            @foreach($pengingatJurnal as $pengingat)
                <li>{{ $pengingat }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h3>Status Presensi Saya Hari Ini</h3>
<p>
    Status: 
    @if($presensiHariIni)
        Masuk: <strong>{{ $presensiHariIni->jam_masuk }}</strong> | 
        Keluar: <strong>{{ $presensiHariIni->jam_keluar ?? 'Belum presensi keluar' }}</strong>
    @else
        <strong>Belum presensi masuk hari ini.</strong> 
        <a href="{{ route('guru.presensi-saya') }}">[ Lakukan Presensi Sekarang ]</a>
    @endif
</p>

<h3>Jadwal Mengajar Hari Ini</h3>
@if($jadwalHariIni->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Jam Ke</th>
                <th>Waktu</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Ruangan</th>
                <th>Status Jurnal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalHariIni as $j)
                @php
                    $isFilled = isset($jurnalHariIni[$j->id_jadwal]);
                @endphp
                <tr>
                    <td>{{ $j->jam_ke }}</td>
                    <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel }}</td>
                    <td>{{ $j->ruang ?? '-' }}</td>
                    <td>
                        @if($isFilled)
                            <span style="color: green; font-weight: bold;">Sudah Diisi</span>
                        @else
                            <span style="color: red; font-weight: bold;">Belum Diisi</span>
                        @endif
                    </td>
                    <td>
                        @if(!$isFilled)
                            <a href="{{ route('jurnal-harian.create', ['id_jadwal' => $j->id_jadwal]) }}">Isi Jurnal</a>
                        @else
                            <a href="{{ route('jurnal-harian.show', $jurnalHariIni[$j->id_jadwal]->id_jurnal) }}">Detail</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada jadwal mengajar untuk Anda hari ini.</p>
@endif
@endsection
