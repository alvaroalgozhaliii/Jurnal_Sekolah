@extends('layouts.app')

@section('content')
<h2>Jurnal Harian</h2>

@if(Auth::user()->isAdmin() || Auth::user()->isPiket())
<form action="{{ route('jurnal-harian.index') }}" method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari mapel, materi, guru, kelas..." style="padding:4px; width:200px;">
    @if(Auth::user()->isAdmin() || Auth::user()->isPiket())
    <label>Filter Tanggal:</label> <input type="date" name="tanggal" value="{{ request('tanggal') }}">
    <label>Guru:</label>
    <select name="id_guru" style="padding:4px;">
        <option value="">-- Semua Guru --</option>
        @foreach($guruList as $g)
        <option value="{{ $g->id_guru }}" {{ request('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama }}</option>
        @endforeach
    </select>
    <label>Kelas:</label>
    <select name="id_kelas" style="padding:4px;">
        <option value="">-- Semua Kelas --</option>
        @foreach($kelasList as $k)
        <option value="{{ $k->id_kelas }}" {{ request('id_kelas') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
        @endforeach
    </select>
    @endif
    <button type="submit">Cari / Filter</button>
    <a href="{{ route('jurnal-harian.index') }}">Reset</a>
</form>
@endif

<a href="{{ route('jurnal-harian.create') }}">+ Tambah Jurnal</a> |
<a href="{{ route('jurnal-harian.trash') }}">Lihat Trash</a><br><br>

<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead><tr><th>No</th><th>Tanggal</th><th>Guru</th><th>Kelas</th><th>Mapel</th><th>Materi</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($jurnal_harian as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->tanggal }}</td>
        <td>{{ $item->guru->nama ?? '-' }}</td>
        <td>{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</td>
        <td>{{ $item->mapel }}</td>
        <td>{{ Str::limit($item->materi, 40) }}</td>
        <td>{{ $item->status_keterlaksanaan }}</td>
        <td>
            <a href="{{ route('jurnal-harian.show', $item->id_jurnal) }}">Detail</a>
            @if(Auth::user()->isGuru() && $item->id_guru === Auth::user()->guru?->id_guru)
            | <a href="{{ route('jurnal-harian.edit', $item->id_jurnal) }}">Edit</a>
            | <form action="{{ route('jurnal-harian.destroy', $item->id_jurnal) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus jurnal ini?')">Hapus</button>
              </form>
            @elseif(Auth::user()->isAdmin())
            | <a href="{{ route('jurnal-harian.edit', $item->id_jurnal) }}">Edit</a>
            | <form action="{{ route('jurnal-harian.destroy', $item->id_jurnal) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus jurnal ini?')">Hapus</button>
              </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;">Tidak ada data jurnal harian.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection