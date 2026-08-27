@extends('layouts.app')

@section('content')
<h2>Data Master: Mata Pelajaran</h2>

<div style="margin-bottom: 15px;">
    <a href="{{ route('mapel.create') }}">+ Tambah Mata Pelajaran Baru</a>
</div>

<form action="{{ route('mapel.index') }}" method="GET" style="margin-bottom: 15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau kode mapel..." style="padding: 5px; width: 250px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('mapel.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Mapel</th>
            <th>Nama Mata Pelajaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mapel as $m)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->kode_mapel ?? '-' }}</td>
            <td>{{ $m->nama_mapel }}</td>
            <td>
                <a href="{{ route('mapel.edit', $m->id_mapel) }}">Edit</a> |
                <form action="{{ route('mapel.destroy', $m->id_mapel) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus mata pelajaran ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red; background:none; border:none; cursor:pointer; text-decoration:underline; padding:0;">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4">Tidak ada data mata pelajaran.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
