@extends('layouts.app')

@section('content')
<h2>Trash - Data Jadwal</h2>
<a href="{{ route('jadwal.index') }}">&#8592; Kembali ke Data Jadwal</a><br><br>

@if($jadwal->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Hari</th><th>Jam</th><th>Kelas</th><th>Guru</th><th>Mapel</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($jadwal as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->hari }}</td>
        <td>{{ $item->jam_ke }}</td>
        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->guru->nama ?? '-' }}</td>
        <td>{{ $item->mapel }}</td>
        <td>
            <form action="{{ route('jadwal.restore', $item->id_jadwal) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('jadwal.forceDelete', $item->id_jadwal) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada jadwal di trash.</p>
@endif
@endsection
