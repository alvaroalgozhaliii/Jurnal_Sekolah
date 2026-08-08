@extends('layouts.app')

@section('content')
<h2>Detail Pengguna: {{ $user->username }}</h2>
<a href="{{ route('pengguna.index') }}">&#8592; Kembali</a> | <a href="{{ route('pengguna.edit', $user->id_user) }}">Edit</a><br><br>

<table border="1" cellpadding="8" style="border-collapse:collapse;">
    <tr><th>Nama</th><td>{{ $user->nama }}</td></tr>
    <tr><th>Username</th><td>{{ $user->username }}</td></tr>
    <tr><th>NIP</th><td>{{ $user->nip ?? '-' }}</td></tr>
    <tr><th>Role</th><td><strong>{{ strtoupper($user->role) }}</strong></td></tr>
    <tr><th>Status</th><td>{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</td></tr>
    <tr><th>Dibuat</th><td>{{ $user->created_at }}</td></tr>
</table>
@endsection
