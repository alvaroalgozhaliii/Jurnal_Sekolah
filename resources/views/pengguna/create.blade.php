@extends('layouts.app')

@section('content')
<h2>Tambah Pengguna Baru</h2>
<a href="{{ route('pengguna.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('pengguna.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Nama Lengkap *</label></td><td><input type="text" name="nama" value="{{ old('nama') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Username *</label></td><td><input type="text" name="username" value="{{ old('username') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Password *</label></td><td><input type="password" name="password" required style="width:300px; padding:5px;"><br><small>Minimal 6 karakter</small></td></tr>
        <tr><td><label>NIP</label></td><td><input type="text" name="nip" value="{{ old('nip') }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Role *</label></td><td>
            <select name="role" required style="padding:5px;">
                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="piket" {{ old('role') == 'piket' ? 'selected' : '' }}>Piket</option>
                <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </td></tr>
        <tr><td><label>Status Aktif</label></td><td><input type="checkbox" name="aktif" value="1" checked> Aktif</td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
@endsection
