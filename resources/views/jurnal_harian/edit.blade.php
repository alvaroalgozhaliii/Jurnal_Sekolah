@extends('layouts.app')

@section('content')
<h2>Edit Jurnal Harian</h2>
<a href="{{ route('jurnal-harian.index') }}">&#8592; Kembali</a><br><br>

<p><strong>Jadwal:</strong> {{ $jurnal_harian->jadwal->hari ?? '-' }} | Jam {{ $jurnal_harian->jadwal->jam_ke ?? '-' }} | {{ $jurnal_harian->jadwal->kelas->nama_kelas ?? '-' }} | {{ $jurnal_harian->mapel }}</p>
<p><strong>Tanggal:</strong> {{ $jurnal_harian->tanggal }}</p>
<p><strong>Guru:</strong> {{ $jurnal_harian->guru->nama ?? '-' }}</p>

<form action="{{ route('jurnal-harian.update', $jurnal_harian->id_jurnal) }}" method="POST">
    @csrf @method('PUT')
    <table>
        <tr><td><label>Materi *</label></td><td><input type="text" name="materi" value="{{ old('materi', $jurnal_harian->materi) }}" required style="width:400px; padding:5px;"></td></tr>
        <tr><td><label>Sub Materi</label></td><td><input type="text" name="sub_materi" value="{{ old('sub_materi', $jurnal_harian->sub_materi) }}" style="width:400px; padding:5px;"></td></tr>
        <tr><td><label>Catatan Pengajaran</label></td><td><textarea name="catatan_pengajaran" rows="4" style="width:400px; padding:5px;">{{ old('catatan_pengajaran', $jurnal_harian->catatan_pengajaran) }}</textarea></td></tr>
        <tr><td><label>Status Keterlaksanaan</label></td><td>
            <select name="status_keterlaksanaan" style="padding:5px;">
                <option value="terlaksana" {{ $jurnal_harian->status_keterlaksanaan == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                <option value="tidak_terlaksana" {{ $jurnal_harian->status_keterlaksanaan == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                <option value="kosong" {{ $jurnal_harian->status_keterlaksanaan == 'kosong' ? 'selected' : '' }}>Kosong</option>
                <option value="pengganti" {{ $jurnal_harian->status_keterlaksanaan == 'pengganti' ? 'selected' : '' }}>Pengganti</option>
            </select>
        </td></tr>
        <tr><td></td><td><button type="submit" style="padding:8px 20px; margin-top:10px;">UPDATE JURNAL</button></td></tr>
    </table>
</form>
@endsection