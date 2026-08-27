@extends('layouts.app')

@section('content')
<h2>Edit Mata Pelajaran</h2>
<a href="{{ route('mapel.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('mapel.update', $mapel->id_mapel) }}" method="POST">
    @csrf
    @method('PUT')
    <table>
        <tr>
            <td><label>Kode Mapel</label></td>
            <td><input type="text" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" style="padding:5px; width:200px;"></td>
        </tr>
        <tr>
            <td><label>Nama Mata Pelajaran *</label></td>
            <td><input type="text" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required style="padding:5px; width:300px;"></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td>
        </tr>
    </table>
</form>
@endsection
