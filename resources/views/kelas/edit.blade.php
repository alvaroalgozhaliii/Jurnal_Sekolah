@extends('layouts.app')

@section('content')
<h2>Edit Kelas: {{ $kelas->nama_kelas }}</h2>
<a href="{{ route('kelas.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('kelas.update', $kelas->id_kelas) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>Nama Kelas *</label></td><td><input type="text" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required style="width:200px; padding:5px;"></td></tr>
        <tr><td><label>Tingkat *</label></td><td>
            <select name="tingkat" required style="padding:5px;">
                <option value="X" {{ $kelas->tingkat == 'X' ? 'selected' : '' }}>X</option>
                <option value="XI" {{ $kelas->tingkat == 'XI' ? 'selected' : '' }}>XI</option>
                <option value="XII" {{ $kelas->tingkat == 'XII' ? 'selected' : '' }}>XII</option>
            </select>
        </td></tr>
        <tr><td><label>Jurusan</label></td><td>
            <select name="id_jurusan" style="width:300px; padding:5px;">
                <option value="">-- Tidak Ada Jurusan --</option>
                @foreach($jurusan as $j)
                <option value="{{ $j->id_jurusan }}" {{ $kelas->id_jurusan == $j->id_jurusan ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Wali Kelas</label></td><td><input type="text" name="wali_kelas" value="{{ old('wali_kelas', $kelas->wali_kelas) }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td></tr>
    </table>
</form>
@endsection