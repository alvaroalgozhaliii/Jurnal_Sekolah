@extends('layouts.app')

@section('content')
<h2>Detail Kelas: {{ $kelas->nama_kelas }}</h2>
<a href="{{ route('kelas.index') }}">&#8592; Kembali ke Data Kelas</a> | 
<a href="{{ route('kelas.edit', $kelas->id_kelas) }}">Edit Info Kelas</a> | 
<a href="{{ route('jadwal.create', ['id_kelas' => $kelas->id_kelas]) }}">+ Tambah Jadwal Kelas Ini</a>
<br><br>

<!-- INFORMASI KELAS -->
<table border="1" cellpadding="8" style="border-collapse:collapse; margin-bottom: 20px;">
    <tr><th style="width:200px;">Nama Kelas</th><td>{{ $kelas->nama_kelas }}</td></tr>
    <tr><th>Tingkat</th><td>{{ $kelas->tingkat }}</td></tr>
    <tr><th>Jurusan</th><td>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
    <tr><th>Wali Kelas</th><td>{{ $kelas->wali_kelas ?? '-' }}</td></tr>
</table>

<!-- JADWAL PELAJARAN KELAS -->
<h3>Jadwal Pelajaran Kelas {{ $kelas->nama_kelas }}</h3>
@php
    $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp

@if($kelas->jadwal->count() > 0)
    @foreach($hariOrder as $h)
        @if(isset($jadwalGrouped[$h]) && count($jadwalGrouped[$h]) > 0)
            <h4 style="margin-top:15px; margin-bottom:5px; color:#0066cc;">{{ $h }}</h4>
            <table border="1" cellpadding="8" style="border-collapse:collapse; margin-bottom:15px;">
                <thead>
                    <tr>
                        <th>Jam Ke</th>
                        <th>Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Ruangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalGrouped[$h] as $j)
                    <tr>
                        <td>Jam ke-{{ $j->jam_ke }}</td>
                        <td>{{ $j->waktu_mulai ? \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') : '-' }} - {{ $j->waktu_selesai ? \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') : '-' }}</td>
                        <td><strong>{{ $j->mapel }}</strong></td>
                        <td>{{ $j->guru->nama ?? '-' }}</td>
                        <td>{{ $j->ruang ?? '-' }}</td>
                        <td>
                            <a href="{{ route('jadwal.edit', $j->id_jadwal) }}">Edit</a> |
                            <form action="{{ route('jadwal.destroy', $j->id_jadwal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color:red; background:none; border:none; cursor:pointer; text-decoration:underline; padding:0;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
@else
    <p>Belum ada jadwal pelajaran untuk kelas ini. <a href="{{ route('jadwal.create', ['id_kelas' => $kelas->id_kelas]) }}">+ Tambah Jadwal</a></p>
@endif

<hr style="margin: 20px 0;">

<!-- MATA PELAJARAN KELAS -->
<h3>Mata Pelajaran Kelas</h3>

<!-- Form Search & Add Mapel -->
<div style="background-color: #f9f9f9; padding: 12px; border: 1px solid #ddd; margin-bottom: 15px;">
    <form action="{{ route('kelas.show', $kelas->id_kelas) }}" method="GET" style="display:inline-block; margin-right: 20px;">
        <label><strong>Cari Mapel:</strong></label>
        <input type="text" name="mapel_search" value="{{ $mapelSearch ?? '' }}" placeholder="Ketik nama mapel..." style="padding:4px;">
        <button type="submit">Cari</button>
        @if(!empty($mapelSearch))
            <a href="{{ route('kelas.show', $kelas->id_kelas) }}">Reset</a>
        @endif
    </form>

    <form action="{{ route('kelas.attach-mapel', $kelas->id_kelas) }}" method="POST" style="display:inline-block;">
        @csrf
        <label><strong>Tambah Mapel ke Kelas:</strong></label>
        <select name="id_mapel" required style="padding:4px; min-width:200px;">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($availableMapel as $mp)
                <option value="{{ $mp->id_mapel }}">{{ $mp->nama_mapel }} ({{ $mp->kode_mapel ?? '-' }})</option>
            @endforeach
        </select>
        <button type="submit">+ Tambah</button>
    </form>
</div>

@if($kelas->mataPelajaran->count() > 0)
    <table border="1" cellpadding="8" style="border-collapse:collapse; margin-bottom:20px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Mapel</th>
                <th>Nama Mata Pelajaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas->mataPelajaran as $mp)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mp->kode_mapel ?? '-' }}</td>
                <td>{{ $mp->nama_mapel }}</td>
                <td>
                    <form action="{{ route('kelas.detach-mapel', [$kelas->id_kelas, $mp->id_mapel]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus mapel dari kelas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color:red; background:none; border:none; cursor:pointer; text-decoration:underline; padding:0;">Hapus dari Kelas</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p style="margin-bottom: 20px;">Belum ada mata pelajaran yang didaftarkan ke kelas ini.</p>
@endif

<hr style="margin: 20px 0;">

<!-- DAFTAR SISWA KELAS -->
<h3>Daftar Siswa ({{ $kelas->siswa->count() }} siswa)</h3>
@if($kelas->siswa->count() > 0)
<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <thead>
        <tr><th>No</th><th>NIS</th><th>Nama</th><th>Jenis Kelamin</th></tr>
    </thead>
    <tbody>
        @foreach($kelas->siswa as $s)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->jenis_kelamin ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>Belum ada siswa di kelas ini.</p>
@endif

@endsection