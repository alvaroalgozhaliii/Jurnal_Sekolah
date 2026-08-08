@extends('layouts.app')

@section('content')
<h2>Data Jadwal Pelajaran</h2>
<a href="{{ route('jadwal.create') }}">+ Tambah Jadwal</a>
&nbsp;|&nbsp;
<a href="{{ route('jadwal.trash') }}">Lihat Trash</a>
<br><br>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead><tr><th>No</th><th>Hari</th><th>Jam</th><th>Waktu</th><th>Kelas</th><th>Guru</th><th>Mapel</th><th>Ruang</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($jadwal as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->hari }}</td>
        <td>{{ $item->jam_ke }}</td>
        <td>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</td>
        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->guru->nama ?? '-' }}</td>
        <td>{{ $item->mapel }}</td>
        <td>{{ $item->ruang ?? '-' }}</td>
        <td>{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
            <a href="{{ route('jadwal.show', $item->id_jadwal) }}">Detail</a> |
            <a href="{{ route('jadwal.edit', $item->id_jadwal) }}">Edit</a> |
            <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="10" style="text-align:center;">Belum ada data jadwal.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection