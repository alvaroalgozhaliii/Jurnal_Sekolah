@extends('layouts.app')

@section('content')
<h2>Tambah Mata Pelajaran Baru</h2>
<a href="{{ route('mapel.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('mapel.store') }}" method="POST">
    @csrf
    <table>
        <tr>
            <td><label>Kode Mapel</label></td>
            <td><input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" placeholder="Contoh: MP-01" style="padding:5px; width:200px;"></td>
        </tr>
        <tr>
            <td><label>Nama Mata Pelajaran *</label></td>
            <td><input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Contoh: Pemrograman Web" style="padding:5px; width:300px;"></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td>
        </tr>
    </table>
</form>
@endsection
