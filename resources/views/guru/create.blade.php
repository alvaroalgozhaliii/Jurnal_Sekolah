@extends('layouts.app')

@section('content')
<h2>Tambah Guru Baru</h2>

@if($errors->any())
    <div style="color:red; border:1px solid red; padding:10px; margin-bottom:15px;">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<a href="{{ route('guru.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('guru.store') }}" method="POST">
    @csrf
    <table>
        <tr>
            <td><label>Nama Lengkap *</label></td>
            <td><input type="text" name="nama" value="{{ old('nama') }}" required style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>NIP</label></td>
            <td><input type="text" name="nip" value="{{ old('nip') }}" style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>Bidang Studi</label></td>
            <td><input type="text" name="bidang_studi" value="{{ old('bidang_studi') }}" style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>No Telepon</label></td>
            <td><input type="text" name="no_telp" value="{{ old('no_telp') }}" style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td colspan="2"><hr><strong>Buat Akun User (Opsional)</strong></td>
        </tr>
        <tr>
            <td><label>Username</label></td>
            <td>
                <input type="text" name="username" value="{{ old('username') }}" style="width:300px; padding:5px;">
                <br><small>Kosongkan jika tidak perlu akun</small>
            </td>
        </tr>
        <tr>
            <td><label>Password</label></td>
            <td>
                <input type="password" name="password" style="width:300px; padding:5px;">
                <br><small>Default: guru123 jika tidak diisi</small>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td>
        </tr>
    </table>
</form>
@endsection