@extends('layouts.app')

@section('title', 'Jadwal Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Waka Bertugas</h1>
        <p class="page-subtitle">Tentukan Waka yang bertugas pada setiap tanggal</p>
    </div>
    <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary">+ Buat Jadwal</a>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Jadwal</h3></div>
    <div class="card-body" style="padding:0;">
        @if(session('success'))<div class="alert alert-success" style="margin:16px;">{{ session('success') }}</div>@endif
        @if($jadwal->count())
        <div class="table-wrapper" style="border:none; border-radius:0;"><table class="table">
            <thead><tr><th>No</th><th>Tanggal</th><th>Waka Bertugas</th><th>Keterangan</th><th class="action-col">Aksi</th></tr></thead>
            <tbody>
            @foreach($jadwal as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->tanggal->format('d-m-Y') }}</td>
                    <td class="fw-bold text-navy">{{ $item->waka->nama ?? '-' }}<br><span class="text-muted" style="font-size:11px;">{{ strtoupper(str_replace('_', ' ', $item->waka->role ?? '-')) }}</span></td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="action-col">
                        <a href="{{ route('waka-kurikulum.jadwal.show', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Detail</a>
                        <a href="{{ route('waka-kurikulum.jadwal.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Ubah</a>
                        <form action="{{ route('waka-kurikulum.jadwal.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
        @else
            <div class="empty-state"><div class="empty-state-text">Belum ada Waka yang dijadwalkan.</div></div>
        @endif
    </div>
</div>
@endsection
