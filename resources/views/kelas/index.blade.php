@extends('layouts.app')

@section('content')
<h2>Data Kelas</h2>
<a href="{{ route('kelas.create') }}">+ Tambah Kelas</a>
&nbsp;|&nbsp;
<a href="{{ route('kelas.trash') }}">Lihat Trash</a>
<br><br>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Nama Kelas</th><th>Tingkat</th><th>Jurusan</th><th>Wali Kelas</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($kelas as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_kelas }}</td>
        <td>{{ $item->tingkat }}</td>
        <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
        <td>{{ $item->wali_kelas ?? '-' }}</td>
        <td>
            <a href="{{ route('kelas.show', $item->id_kelas) }}">Detail</a> |
            <a href="{{ route('kelas.edit', $item->id_kelas) }}">Edit</a> |
            <form action="{{ route('kelas.destroy', $item->id_kelas) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus kelas ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;">Belum ada data kelas.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection