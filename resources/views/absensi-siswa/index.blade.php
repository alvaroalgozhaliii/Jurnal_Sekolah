@extends('layouts.app')

@section('content')
<h2>Data Absensi Siswa</h2>
@if(!Auth::user()->isSiswa())
<a href="{{ route('absensi-siswa.create') }}">+ Input Absensi Siswa</a><br><br>
@endif

<form action="{{ route('absensi-siswa.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama siswa, NIS, kelas, atau status..." style="padding:5px; width:280px;">
    <button type="submit">Cari</button>
    @if(!empty($search))
        <a href="{{ route('absensi-siswa.index') }}">Reset</a>
    @endif
</form>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead><tr><th>No</th><th>Tanggal Jurnal</th><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Status</th><th>Keterangan</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($absensi as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->jurnal->tanggal ?? '-' }}</td>
        <td>{{ $item->siswa->nama ?? '-' }}</td>
        <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->jurnal->mapel ?? '-' }}</td>
        <td><strong>{{ strtoupper($item->status) }}</strong></td>
        <td>{{ $item->keterangan ?? '-' }}</td>
        <td>
            <a href="{{ route('absensi-siswa.show', $item->id_absensi) }}">Detail</a>
            @if(!Auth::user()->isSiswa())
            | <a href="{{ route('absensi-siswa.edit', $item->id_absensi) }}">Edit</a>
            | <form action="{{ route('absensi-siswa.destroy', $item->id_absensi) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus data absensi?')">Hapus</button>
              </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;">Tidak ada data absensi siswa.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection