@extends('layouts.app')

@section('content')
<h2>Edit Jurusan: {{ $jurusan->nama_jurusan }}</h2>
<a href="{{ route('jurusan.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('jurusan.update', $jurusan->id_jurusan) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>Nama Jurusan *</label></td><td><input type="text" name="nama_jurusan" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Kode / Singkatan (Rombel) *</label></td><td><input type="text" name="rombel" value="{{ old('rombel', $jurusan->rombel) }}" required style="width:100px; padding:5px;"></td></tr>
        <tr><td><label>Maks Rombel *</label></td><td><input type="number" name="maks_rombel" value="{{ old('maks_rombel', $jurusan->maks_rombel) }}" min="1" required style="width:80px; padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td></tr>
    </table>
</form>
@endsection
