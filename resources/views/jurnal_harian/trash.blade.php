@extends('layouts.app')

@section('content')
<h2>Trash - Jurnal Harian</h2>
<a href="{{ route('jurnal-harian.index') }}">&#8592; Kembali ke Jurnal Harian</a><br><br>

@if($jurnal_harian->count() > 0)
<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead><tr><th>No</th><th>Tanggal</th><th>Guru</th><th>Kelas</th><th>Mapel</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($jurnal_harian as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->tanggal }}</td>
        <td>{{ $item->guru->nama ?? '-' }}</td>
        <td>{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->mapel }}</td>
        <td>
            <form action="{{ route('jurnal-harian.restore', $item->id_jurnal) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" style="color:green;">Restore</button>
            </form>
            |
            <form action="{{ route('jurnal-harian.forceDelete', $item->id_jurnal) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;" onclick="return confirm('Hapus permanen?')">Hapus Permanen</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>Tidak ada jurnal di trash.</p>
@endif
@endsection