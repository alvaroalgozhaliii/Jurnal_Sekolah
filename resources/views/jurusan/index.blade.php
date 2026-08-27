@extends('layouts.app')

@section('content')
<h2>Data Jurusan</h2>
<a href="{{ route('jurusan.create') }}">+ Tambah Jurusan</a>
&nbsp;|&nbsp;
<a href="{{ route('jurusan.trash') }}">Lihat Trash</a>
<br><br>

<form action="{{ route('jurusan.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama jurusan atau kode rombel..." style="padding:5px; width:250px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('jurusan.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Nama Jurusan</th><th>Kode Rombel</th><th>Maks Rombel</th><th>Jumlah Kelas</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($jurusan as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_jurusan }}</td>
        <td>{{ $item->rombel }}</td>
        <td>{{ $item->maks_rombel }}</td>
        <td>{{ $item->kelas ? $item->kelas->count() : 0 }}</td>
        <td>
            <a href="{{ route('jurusan.show', $item->id_jurusan) }}">Detail</a> |
            <a href="{{ route('jurusan.edit', $item->id_jurusan) }}">Edit</a> |
            <form action="{{ route('jurusan.destroy', $item->id_jurusan) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus jurusan ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;">Belum ada data jurusan.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
