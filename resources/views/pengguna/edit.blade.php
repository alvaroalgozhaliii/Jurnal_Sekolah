@extends('layouts.app')

@section('content')
<h2>Edit Pengguna: {{ $user->username }}</h2>
<a href="{{ route('pengguna.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('pengguna.update', $user->id_user) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>Nama Lengkap *</label></td><td><input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Username *</label></td><td><input type="text" name="username" value="{{ old('username', $user->username) }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Password Baru</label></td><td><input type="password" name="password" style="width:300px; padding:5px;"><br><small>Kosongkan jika tidak ingin mengubah password</small></td></tr>
        <tr><td><label>NIP</label></td><td><input type="text" name="nip" value="{{ old('nip', $user->nip) }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Role *</label></td><td>
            <select name="role" required style="padding:5px;">
                <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="piket" {{ $user->role == 'piket' ? 'selected' : '' }}>Piket</option>
                <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </td></tr>
        <tr><td><label>Status Aktif</label></td><td><input type="checkbox" name="aktif" value="1" {{ $user->aktif ? 'checked' : '' }}> Aktif</td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td></tr>
    </table>
</form>
@endsection
