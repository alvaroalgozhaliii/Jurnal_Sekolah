@extends('layouts.app')

@section('content')
<h2>Edit Data Siswa: {{ $siswa->nama }}</h2>
<a href="{{ route('siswa.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('siswa.update', $siswa->id_siswa) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>NIS *</label></td><td><input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Nama Lengkap *</label></td><td><input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}" required style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Kelas *</label></td><td>
            <select name="id_kelas" required style="width:300px; padding:5px;">
                @foreach($kelas as $k)
                <option value="{{ $k->id_kelas }}" {{ $siswa->id_kelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Jenis Kelamin</label></td><td>
            <select name="jenis_kelamin" style="padding:5px;">
                <option value="">-- Pilih --</option>
                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </td></tr>
        <tr><td><label>Tempat Lahir</label></td><td><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Tanggal Lahir</label></td><td><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" style="padding:5px;"></td></tr>
        <tr><td><label>No Telp Orang Tua</label></td><td><input type="text" name="no_telp_ortu" value="{{ old('no_telp_ortu', $siswa->no_telp_ortu) }}" style="width:300px; padding:5px;"></td></tr>
        <tr><td><label>Status</label></td><td>
            <select name="aktif" style="padding:5px;">
                <option value="1" {{ $siswa->aktif ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$siswa->aktif ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </td></tr>
        @if($siswa->user)
        <tr><td colspan="2"><hr><strong>Akun User Terhubung:</strong> {{ $siswa->user->username }}</td></tr>
        @endif
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE</button></td></tr>
    </table>
</form>
@endsection