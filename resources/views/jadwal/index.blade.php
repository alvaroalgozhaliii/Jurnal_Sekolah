@extends('layouts.app')

@section('content')
<h2>Data Master: Jadwal Pelajaran</h2>

<div style="margin-bottom:15px;">
    <a href="{{ route('jadwal.create') }}">+ Tambah Jadwal Baru</a> &nbsp;|&nbsp;
    <a href="{{ route('jadwal.trash') }}">Lihat Trash</a>
</div>

<form action="{{ route('jadwal.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari hari, kelas, guru, mapel, ruang, jam ke..." style="padding:5px; width:300px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('jadwal.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead>
        <tr>
            <th>No</th>
            <th>Hari</th>
            <th>Jam Ke</th>
            <th>Waktu KBM</th>
            <th>Kelas</th>
            <th>Guru Pengajar</th>
            <th>Mata Pelajaran</th>
            <th>Ruangan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    @forelse($jadwal as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td><strong>{{ $item->hari }}</strong></td>
        <td>Jam ke-{{ $item->jam_ke }}</td>
        <td>{{ $item->waktu_mulai ? \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') : '-' }} - {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') : '-' }}</td>
        <td><strong>{{ $item->kelas->nama_kelas ?? '-' }}</strong></td>
        <td>{{ $item->guru->nama ?? '-' }}</td>
        <td>{{ $item->mapel }}</td>
        <td>{{ $item->ruang ?? '-' }}</td>
        <td>{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
            <a href="{{ route('jadwal.show', $item->id_jadwal) }}">Detail</a> |
            <a href="{{ route('jadwal.edit', $item->id_jadwal) }}">Edit</a> |
            <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?');">
                @csrf @method('DELETE')
                <button type="submit" style="color:red; background:none; border:none; cursor:pointer; text-decoration:underline; padding:0;">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="10" style="text-align:center;">Belum ada data jadwal.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection