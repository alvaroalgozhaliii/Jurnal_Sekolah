@extends('layouts.app')

@section('content')
<h2>Data Guru</h2>

@if(session('success'))
    <p style="color:green; font-weight:bold;">{{ session('success') }}</p>
@endif
@if(session('error'))
    <p style="color:red; font-weight:bold;">{{ session('error') }}</p>
@endif

<a href="{{ route('guru.create') }}">+ Tambah Guru</a>
&nbsp;|&nbsp;
<a href="{{ route('guru.trash') }}">Lihat Trash</a>
<br><br>

<form action="{{ route('guru.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NIP, atau bidang studi..." style="padding:5px; width:250px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('guru.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>Bidang Studi</th>
            <th>No Telp</th>
            <th>Akun User</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($guru as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->nip ?? '-' }}</td>
            <td>{{ $item->bidang_studi ?? '-' }}</td>
            <td>{{ $item->no_telp ?? '-' }}</td>
            <td>{{ $item->user->username ?? 'Belum ada akun' }}</td>
            <td>
                <a href="{{ route('guru.show', $item->id_guru) }}">Detail</a>
                &nbsp;|&nbsp;
                <a href="{{ route('guru.edit', $item->id_guru) }}">Edit</a>
                &nbsp;|&nbsp;
                <form action="{{ route('guru.destroy', $item->id_guru) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus data guru ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;">Belum ada data guru.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection