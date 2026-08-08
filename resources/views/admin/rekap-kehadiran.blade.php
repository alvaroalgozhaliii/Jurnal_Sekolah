@extends('layouts.app')

@section('content')
<h2>Rekap Kehadiran Siswa & Guru</h2>

<form action="{{ route('admin.rekap-kehadiran') }}" method="GET" style="margin-bottom: 20px;">
    <label>Tanggal:</label>
    <input type="date" name="tanggal" value="{{ $tanggal }}">

    <label>Filter Kelas (Siswa):</label>
    <select name="id_kelas">
        <option value="">-- Semua Kelas --</option>
        @foreach($kelas as $k)
            <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
        @endforeach
    </select>

    <label>Filter Guru:</label>
    <select name="id_guru">
        <option value="">-- Semua Guru --</option>
        @foreach($guru as $g)
            <option value="{{ $g->id_guru }}" {{ $guruId == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
        @endforeach
    </select>

    <button type="submit">Filter Rekap</button>
</form>

<h3>Rekap Kehadiran Guru (Tanggal: {{ $tanggal }})</h3>
@if($rekapGuru->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapGuru as $index => $rg)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rg->user->nama ?? '-' }}</td>
                    <td>{{ $rg->user->nip ?? '-' }}</td>
                    <td>{{ $rg->jam_masuk }}</td>
                    <td>{{ $rg->jam_keluar ?? 'Belum keluar' }}</td>
                    <td>{{ $rg->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada data presensi guru pada tanggal ini.</p>
@endif

<br>
<h3>Rekap Kehadiran Siswa (Tanggal: {{ $tanggal }})</h3>
@if($rekapSiswa->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapSiswa as $index => $rs)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rs->siswa->nis ?? '-' }}</td>
                    <td>{{ $rs->siswa->nama ?? '-' }}</td>
                    <td>{{ $rs->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $rs->jurnal->mapel ?? '-' }}</td>
                    <td><strong>{{ strtoupper($rs->status) }}</strong></td>
                    <td>{{ $rs->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada data rekap absensi siswa pada tanggal ini.</p>
@endif
@endsection
