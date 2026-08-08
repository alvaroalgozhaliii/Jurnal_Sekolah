@extends('layouts.app')

@section('content')
<h2>Trash - Data Siswa</h2>
<a href="{{ route('siswa.index') }}">&#8592; Kembali ke Data Siswa</a><br><br>

@if($siswa->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Dihapus</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($siswa as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nis }}</td>
        <td>{{ $item->nama }}</td>
        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->deleted_at }}</td>
        <td>
            <form action="{{ route('siswa.restore', $item->id_siswa) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('siswa.forceDelete', $item->id_siswa) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada data siswa di trash.</p>
@endif
@endsection
