@extends('layouts.app')

@section('content')
<h2>Tambah Jurusan Baru</h2>
<a href="{{ route('jurusan.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('jurusan.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Nama Jurusan *</label></td><td><input type="text" name="nama_jurusan" value="{{ old('nama_jurusan') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Kode / Singkatan (Rombel) *</label></td><td><input type="text" name="rombel" value="{{ old('rombel') }}" required style="width:100px; padding:5px;" placeholder="Contoh: RPL"></td></tr>
        <tr><td><label>Maks Rombel *</label></td><td><input type="number" name="maks_rombel" value="{{ old('maks_rombel', 2) }}" min="1" required style="width:80px; padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
@endsection
