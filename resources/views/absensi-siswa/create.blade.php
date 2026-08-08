@extends('layouts.app')

@section('content')
<h2>Input Absensi Siswa (Batch)</h2>
<a href="{{ route('absensi-siswa.index') }}">&#8592; Kembali</a><br><br>

<form action="{{ route('absensi-siswa.create') }}" method="GET" style="margin-bottom:15px;">
    <label><strong>Pilih Jurnal Harian:</strong></label>
    <select name="id_jurnal" onchange="this.form.submit()" style="width:400px; padding:5px;">
        <option value="">-- Pilih Jurnal --</option>
        @foreach($jurnalList as $j)
        <option value="{{ $j->id_jurnal }}" {{ ($jurnalSelected && $jurnalSelected->id_jurnal == $j->id_jurnal) ? 'selected' : '' }}>
            {{ $j->tanggal }} | {{ $j->mapel }} | Kelas {{ $j->jadwal->kelas->nama_kelas ?? '-' }}
        </option>
        @endforeach
    </select>
</form>

@if($jurnalSelected)
<p>Jurnal: <strong>{{ $jurnalSelected->tanggal }} - {{ $jurnalSelected->mapel }} - Kelas {{ $jurnalSelected->jadwal->kelas->nama_kelas ?? '-' }}</strong></p>

@if($siswaList->count() > 0)
<form action="{{ route('absensi-siswa.storeBatch') }}" method="POST">
    @csrf
    <input type="hidden" name="id_jurnal" value="{{ $jurnalSelected->id_jurnal }}">
    <table border="1" cellpadding="8" style="border-collapse:collapse;">
        <thead><tr><th>No</th><th>NIS</th><th>Nama Siswa</th><th>Status *</th><th>Keterangan</th></tr></thead>
        <tbody>
        @foreach($siswaList as $s)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->nama }}</td>
            <td>
                <select name="absensi[{{ $s->id_siswa }}]" required style="padding:4px;">
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpa">Alpa</option>
                    <option value="terlambat">Terlambat</option>
                </select>
            </td>
            <td><input type="text" name="keterangan[{{ $s->id_siswa }}]" placeholder="Opsional" style="width:200px; padding:4px;"></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <br>
    <button type="submit" style="padding:8px 20px;">SIMPAN ABSENSI BATCH</button>
</form>
@else
<p>Tidak ada siswa di kelas ini.</p>
@endif
@else
<p>Silakan pilih jurnal terlebih dahulu dari dropdown di atas.</p>
@endif
@endsection