@extends('layouts.app')

@section('content')
<h2>Detail Jurnal Harian</h2>
<a href="{{ route('jurnal-harian.index') }}">&#8592; Kembali</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Tanggal</th><td>{{ $jurnal_harian->tanggal }}</td></tr>
    <tr><th>Guru</th><td>{{ $jurnal_harian->guru->nama ?? '-' }}</td></tr>
    <tr><th>Kelas</th><td>{{ $jurnal_harian->jadwal->kelas->nama_kelas ?? '-' }}</td></tr>
    <tr><th>Mata Pelajaran</th><td>{{ $jurnal_harian->mapel }}</td></tr>
    <tr><th>Hari / Jam Ke</th><td>{{ $jurnal_harian->jadwal->hari ?? '-' }} / Jam ke-{{ $jurnal_harian->jadwal->jam_ke ?? '-' }}</td></tr>
    <tr><th>Waktu</th><td>{{ $jurnal_harian->jadwal->waktu_mulai ?? '-' }} - {{ $jurnal_harian->jadwal->waktu_selesai ?? '-' }}</td></tr>
    <tr><th>Materi</th><td>{{ $jurnal_harian->materi }}</td></tr>
    <tr><th>Sub Materi</th><td>{{ $jurnal_harian->sub_materi ?? '-' }}</td></tr>
    <tr><th>Catatan Pengajaran</th><td>{{ $jurnal_harian->catatan_pengajaran ?? '-' }}</td></tr>
    <tr><th>Status</th><td><strong>{{ strtoupper(str_replace('_', ' ', $jurnal_harian->status_keterlaksanaan)) }}</strong></td></tr>
</table>

<h3>Absensi Siswa pada Jurnal Ini</h3>
@if($jurnal_harian->absensiSiswa && $jurnal_harian->absensiSiswa->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>No</th><th>NIS</th><th>Nama Siswa</th><th>Status</th><th>Keterangan</th></tr>
    @foreach($jurnal_harian->absensiSiswa as $ab)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $ab->siswa->nis ?? '-' }}</td>
        <td>{{ $ab->siswa->nama ?? '-' }}</td>
        <td>{{ strtoupper($ab->status) }}</td>
        <td>{{ $ab->keterangan ?? '-' }}</td>
    </tr>
    @endforeach
</table>
@else
<p>Belum ada data absensi siswa untuk jurnal ini. <a href="{{ route('absensi-siswa.create', ['id_jurnal' => $jurnal_harian->id_jurnal]) }}">Isi Absensi Siswa</a></p>
@endif
@endsection