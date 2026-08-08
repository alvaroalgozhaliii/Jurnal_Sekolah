@extends('layouts.app')

@section('content')
<h2>Tambah Jadwal Baru</h2>
<a href="{{ route('jadwal.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('jadwal.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Hari *</label></td><td>
            <select name="hari" required style="padding:5px;">
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Jam Ke *</label></td><td><input type="number" name="jam_ke" value="{{ old('jam_ke') }}" min="1" max="15" required style="width:80px; padding:5px;"></td></tr>
        <tr><td><label>Kelas *</label></td><td>
            <select name="id_kelas" required style="width:300px; padding:5px;">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Guru</label></td><td>
            <select name="id_guru" style="width:300px; padding:5px;">
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Mata Pelajaran *</label></td><td><input type="text" name="mapel" value="{{ old('mapel') }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Ruang</label></td><td><input type="text" name="ruang" value="{{ old('ruang') }}" style="width:150px; padding:5px;"></td></tr>
        <tr><td><label>Waktu Mulai</label></td><td><input type="time" name="waktu_mulai" value="{{ old('waktu_mulai') }}" style="padding:5px;"></td></tr>
        <tr><td><label>Waktu Selesai</label></td><td><input type="time" name="waktu_selesai" value="{{ old('waktu_selesai') }}" style="padding:5px;"></td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN</button></td></tr>
    </table>
</form>
@endsection