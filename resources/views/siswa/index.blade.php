@extends('layouts.app')

@section('content')
<h2>Data Siswa</h2>
<a href="{{ route('siswa.create') }}">+ Tambah Siswa</a>
&nbsp;|&nbsp;
<a href="{{ route('siswa.trash') }}">Lihat Trash</a>
<br><br>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>JK</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    @forelse($siswa as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nis }}</td>
        <td>{{ $item->nama }}</td>
        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->jenis_kelamin ?? '-' }}</td>
        <td>{{ $item->aktif ? 'Aktif' : 'Tidak Aktif' }}</td>
        <td>
            <a href="{{ route('siswa.show', $item->id_siswa) }}">Detail</a> |
            <a href="{{ route('siswa.edit', $item->id_siswa) }}">Edit</a> |
            <form action="{{ route('siswa.destroy', $item->id_siswa) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus data siswa ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;">Belum ada data siswa.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection