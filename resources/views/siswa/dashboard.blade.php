@extends('layouts.app')

@section('content')
<h2>Dashboard Siswa</h2>

@if($siswa)
    <p>Selamat Datang, <strong>{{ $siswa->nama }}</strong></p>
    <p>NIS: <strong>{{ $siswa->nis }}</strong> | Kelas: <strong>{{ $siswa->kelas->nama_kelas ?? '-' }}</strong> | Jurusan: <strong>{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</strong></p>

    <h3>Jadwal Pelajaran Kelas Saya Hari Ini</h3>
    @if($jadwalHariIni->count() > 0)
        <table>
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
                @foreach($jadwalHariIni as $j)
                    <tr>
                        <td>{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</td>
                        <td>{{ $j->mapel }}</td>
                        <td>{{ $j->guru->nama ?? '-' }}</td>
                        <td>{{ $j->ruang ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada jadwal pelajaran hari ini.</p>
    @endif

    <br>
    <h3>Status Kehadiran Saya Hari Ini</h3>
    @if($statusPresensi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Status Presensi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statusPresensi as $p)
                    <tr>
                        <td>{{ $p->jurnal->mapel ?? '-' }}</td>
                        <td><strong>{{ strtoupper($p->status) }}</strong></td>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada catatan presensi hari ini.</p>
    @endif
@else
    <p>Data siswa tidak terhubung dengan akun Anda.</p>
@endif
@endsection
