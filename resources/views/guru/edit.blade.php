@extends('layouts.app')

@section('content')
<h2>Edit Data Guru: {{ $guru->nama }}</h2>

@if($errors->any())
    <div style="color:red; border:1px solid red; padding:10px; margin-bottom:15px;">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<a href="{{ route('guru.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('guru.update', $guru->id_guru) }}" method="POST">
    @csrf
    @method('PUT')
    <table>
        <tr>
            <td><label>Nama Lengkap *</label></td>
            <td><input type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>NIP</label></td>
            <td><input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>Bidang Studi</label></td>
            <td><input type="text" name="bidang_studi" value="{{ old('bidang_studi', $guru->bidang_studi) }}" style="width:300px; padding:5px;"></td>
        </tr>
        <tr>
            <td><label>No Telepon</label></td>
            <td><input type="text" name="no_telp" value="{{ old('no_telp', $guru->no_telp) }}" style="width:300px; padding:5px;"></td>
        </tr>
        @if($guru->user)
        <tr>
            <td colspan="2"><hr><strong>Akun User Terhubung:</strong> {{ $guru->user->username }}</td>
        </tr>
        @endif
        <tr>
            <td></td>
            <td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td>
        </tr>
    </table>
</form>
@endsection