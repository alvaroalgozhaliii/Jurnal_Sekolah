@extends('layouts.app')

@section('content')
<h2>Tambah Siswa Baru</h2>
<a href="{{ route('siswa.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('siswa.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>NIS *</label></td><td><input type="text" name="nis" value="{{ old('nis') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Nama Lengkap *</label></td><td><input type="text" name="nama" value="{{ old('nama') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Kelas *</label></td><td>
            <select name="id_kelas" required style="width:300px; padding:5px;">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Jenis Kelamin</label></td><td>
            <select name="jenis_kelamin" style="padding:5px;">
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </td></tr>
        <tr><td><label>Tempat Lahir</label></td><td><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Tanggal Lahir</label></td><td><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" style="padding:5px;"></td></tr>
        <tr><td><label>No Telp Orang Tua</label></td><td><input type="text" name="no_telp_ortu" value="{{ old('no_telp_ortu') }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td colspan="2"><hr><strong>Buat Akun User (Opsional)</strong></td></tr>
        <tr><td><label>Username</label></td><td><input type="text" name="username" value="{{ old('username') }}" style="width:300px; padding:5px;"><br><small>Kosongkan jika tidak perlu akun</small></td></tr>
        <tr><td><label>Password</label></td><td><input type="password" name="password" style="width:300px; padding:5px;"><br><small>Default: siswa123</small></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
@endsection