@extends('layouts.app')

@section('content')
<h2>Tambah Jurnal Harian</h2>
<a href="{{ route('jurnal-harian.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('jurnal-harian.store') }}" method="POST">
    @csrf
    <table>
        <tr><td><label>Pilih Jadwal *</label></td><td>
            <select name="id_jadwal" required style="width:400px; padding:5px;">
                <option value="">-- Pilih Jadwal Mengajar --</option>
                @foreach($jadwalList as $j)
                <option value="{{ $j->id_jadwal }}" {{ (old('id_jadwal') == $j->id_jadwal || request('id_jadwal') == $j->id_jadwal) ? 'selected' : '' }}>
                    {{ $j->hari }} | Jam {{ $j->jam_ke }} | {{ $j->kelas->nama_kelas ?? '-' }} | {{ $j->mapel }} ({{ $j->waktu_mulai }}-{{ $j->waktu_selesai }})
                </option>
                @endforeach
            </select>
        </td></tr>
        <tr><td><label>Tanggal *</label></td><td><input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required style="padding:5px;"></td></tr>
        <tr><td><label>Materi *</label></td><td><input type="text" name="materi" value="{{ old('materi') }}" required style="width:400px; padding:5px;"></td></tr>
        <tr><td><label>Sub Materi</label></td><td><input type="text" name="sub_materi" value="{{ old('sub_materi') }}" style="width:400px; padding:5px;"></td></tr>
        <tr><td><label>Catatan Pengajaran</label></td><td><textarea name="catatan_pengajaran" rows="4" style="width:400px; padding:5px;">{{ old('catatan_pengajaran') }}</textarea></td></tr>
        <tr><td><label>Status Keterlaksanaan</label></td><td>
            <select name="status_keterlaksanaan" style="padding:5px;">
                <option value="terlaksana" {{ old('status_keterlaksanaan') == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                <option value="tidak_terlaksana" {{ old('status_keterlaksanaan') == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                <option value="kosong" {{ old('status_keterlaksanaan') == 'kosong' ? 'selected' : '' }}>Kosong</option>
                <option value="pengganti" {{ old('status_keterlaksanaan') == 'pengganti' ? 'selected' : '' }}>Pengganti</option>
            </select>
        </td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">SIMPAN JURNAL</button></td></tr>
    </table>
</form>
@endsection