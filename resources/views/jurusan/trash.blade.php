@extends('layouts.app')

@section('content')
<h2>Trash - Data Jurusan</h2>
<a href="{{ route('jurusan.index') }}">&#8592; Kembali ke Data Jurusan</a><br><br>

@if($jurusan->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Nama Jurusan</th><th>Kode Rombel</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($jurusan as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_jurusan }}</td>
        <td>{{ $item->rombel }}</td>
        <td>
            <form action="{{ route('jurusan.restore', $item->id_jurusan) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('jurusan.forceDelete', $item->id_jurusan) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada jurusan di trash.</p>
@endif
@endsection
