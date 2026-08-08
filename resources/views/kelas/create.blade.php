@extends('layouts.app')

@section('content')
<h2>Tambah Kelas Baru</h2>
<a href="{{ route('kelas.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('kelas.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Nama Kelas *</label></td><td><input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" required style="width:200px; padding:5px;" placeholder="Contoh: X RPL 1"></td></tr>
        <tr><td><label>Tingkat *</label></td><td>
            <select name="tingkat" required style="padding:5px;">
                <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
            </select>
        </td></tr>
        <tr><td><label>Jurusan</label></td><td>
            <select name="id_jurusan" style="width:300px; padding:5px;">
                <option value="">-- Tidak Ada Jurusan --</option>
                @foreach($jurusan as $j)
                <option value="{{ $j->id_jurusan }}" {{ old('id_jurusan') == $j->id_jurusan ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Wali Kelas</label></td><td><input type="text" name="wali_kelas" value="{{ old('wali_kelas') }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
@endsection