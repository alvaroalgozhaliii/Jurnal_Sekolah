@extends('layouts.app')

@section('content')
<h2>Trash - Data Guru</h2>
<a href="{{ route('guru.index') }}">&#8592; Kembali ke Data Guru</a><br><br>

@if($guru->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>No</th><th>Nama</th><th>NIP</th><th>Bidang Studi</th><th>Dihapus</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    @foreach($guru as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama }}</td>
        <td>{{ $item->nip ?? '-' }}</td>
        <td>{{ $item->bidang_studi ?? '-' }}</td>
        <td>{{ $item->deleted_at }}</td>
        <td>
            <form action="{{ route('guru.restore', $item->id_guru) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('guru.forceDelete', $item->id_guru) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen? Data tidak bisa dikembalikan!')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada data guru di trash.</p>
@endif
@endsection
