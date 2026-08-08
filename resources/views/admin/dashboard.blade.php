@extends('layouts.app')

@section('content')
<h2>Dashboard Admin</h2>

<p>Tahun Pelajaran Aktif: <strong>{{ $tahunAktif ? $tahunAktif->tahun . ' (' . $tahunAktif->semester . ')' : 'Belum ditentukan' }}</strong></p>

<table style="width: 100%; border: none;">
    <tr style="border: none;">
        <td style="border: 1px solid #ccc; padding: 15px; width: 20%;">
            <h3>{{ $jumlahGuru }}</h3>
            <p>Jumlah Guru</p>
        </td>
        <td style="border: 1px solid #ccc; padding: 15px; width: 20%;">
            <h3>{{ $jumlahSiswa }}</h3>
            <p>Jumlah Siswa</p>
        </td>
        <td style="border: 1px solid #ccc; padding: 15px; width: 20%;">
            <h3>{{ $jumlahKelas }}</h3>
            <p>Jumlah Kelas</p>
        </td>
        <td style="border: 1px solid #ccc; padding: 15px; width: 20%;">
            <h3>{{ $jumlahJurusan }}</h3>
            <p>Jumlah Jurusan</p>
        </td>
        <td style="border: 1px solid #ccc; padding: 15px; width: 20%;">
            <h3>{{ $jumlahMapel }}</h3>
            <p>Mata Pelajaran</p>
        </td>
    </tr>
</table>

<br>

<h3>Kehadiran & Jurnal Hari Ini</h3>
<table>
    <tr>
        <th>Indikator</th>
        <th>Jumlah Hari Ini</th>
    </tr>
    <tr>
        <td>Kehadiran Guru (Presensi Masuk)</td>
        <td><strong>{{ $kehadiranGuruHariIni }}</strong> guru</td>
    </tr>
    <tr>
        <td>Kehadiran Siswa (Hadir)</td>
        <td><strong>{{ $kehadiranSiswaHariIni }}</strong> siswa</td>
    </tr>
    <tr>
        <td>Jurnal Harian Terisi</td>
        <td><strong>{{ $jurnalHariIni }}</strong> jurnal</td>
    </tr>
</table>
@endsection
