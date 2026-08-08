@extends('layouts.app')

@section('content')
<h2>Jadwal Pelajaran Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}</h2>

@if(count($jadwal) > 0)
    @foreach($jadwal as $hari => $items)
        <h3>Hari: {{ $hari }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Jam Ke</th>
                    <th>Waktu</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $j)
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
    @endforeach
@else
    <p>Belum ada jadwal pelajaran.</p>
@endif
@endsection
