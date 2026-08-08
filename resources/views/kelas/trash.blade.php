@extends('layouts.app')

@section('content')
<h2>Trash - Data Kelas</h2>
<a href="{{ route('kelas.index') }}">&#8592; Kembali ke Data Kelas</a><br><br>

@if($kelas->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Nama Kelas</th><th>Tingkat</th><th>Jurusan</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($kelas as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_kelas }}</td>
        <td>{{ $item->tingkat }}</td>
        <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
        <td>
            <form action="{{ route('kelas.restore', $item->id_kelas) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('kelas.forceDelete', $item->id_kelas) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada data kelas di trash.</p>
@endif
@endsection
