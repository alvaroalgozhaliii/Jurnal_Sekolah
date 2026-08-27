@extends('layouts.app')

@section('content')
<h2>Manajemen Pengguna (User)</h2>
<a href="{{ route('pengguna.create') }}">+ Tambah Pengguna</a><br><br>

<form action="{{ route('pengguna.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, username, NIP, role..." style="padding:5px; width:250px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('pengguna.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>NIP</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($users as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama }}</td>
        <td>{{ $item->username }}</td>
        <td>{{ $item->nip ?? '-' }}</td>
        <td><strong>{{ strtoupper($item->role) }}</strong></td>
        <td>{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
            <a href="{{ route('pengguna.show', $item->id_user) }}">Detail</a> |
            <a href="{{ route('pengguna.edit', $item->id_user) }}">Edit</a> |
            @if($item->id_user !== Auth::id())
            <form action="{{ route('pengguna.destroy', $item->id_user) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus pengguna ini?')">Hapus</button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;">Belum ada data pengguna.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
