@extends('layouts.app')

@section('title', 'Jadwal Piket & Waka Bertugas — Jurnal Sekolah')
@section('page-title', 'Jadwal Piket & Waka Bertugas')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jadwal Piket & Waka Bertugas</h1>
        <p class="page-subtitle">Manajemen penugasan Waka Piket Harian dan Guru Piket per tanggal</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary" style="font-weight:600;">+ Buat Jadwal Baru</a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <svg class="svg-icon text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Daftar Penugasan Harian
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if(session('success'))
            <div class="alert alert-success" style="margin:16px;">{{ session('success') }}</div>
        @endif

        @if($jadwal->count())
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Tanggal</th>
                        <th>Waka yang Bertugas</th>
                        <th>Guru Piket</th>
                        <th>Keterangan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($jadwal as $index => $item)
                    @php
                        $isToday = $item->tanggal ? $item->tanggal->isToday() : false;
                    @endphp
                    <tr style="{{ $isToday ? 'background:#f0fdf4;' : '' }}">
                        <td>{{ $jadwal->firstItem() + $index }}</td>
                        <td class="fw-bold">
                            {{ $item->tanggal ? $item->tanggal->translatedFormat('l, d F Y') : '-' }}
                            @if($isToday)
                                <span class="badge" style="background:#16a34a; color:#fff; font-size:10px; margin-left:4px;">HARI INI</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-navy">{{ $item->waka->nama ?? '-' }}</strong>
                            <div class="text-muted" style="font-size:11.5px;">
                                {{ strtoupper(str_replace('_', ' ', $item->waka->role ?? '-')) }}
                                @if($item->waka && $item->waka->no_hp)
                                    &bull; 📱 {{ $item->waka->no_hp }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->guruPiket)
                                <span class="fw-bold">{{ $item->guruPiket->nama }}</span>
                                <div class="text-muted" style="font-size:11.5px;">
                                    {{ $item->guruPiket->bidang_studi ?? 'Guru Piket' }}
                                </div>
                            @else
                                <span class="text-muted"><em>Belum ditentukan</em></span>
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="action-col">
                            <a href="{{ route('waka-kurikulum.jadwal.edit', $item->id_jadwal_waka) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="{{ route('waka-kurikulum.jadwal.destroy', $item->id_jadwal_waka) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penugasan jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($jadwal->hasPages())
            <div style="padding:16px;">
                {{ $jadwal->links() }}
            </div>
        @endif

        @else
            <div class="empty-state" style="padding:40px 20px; text-align:center;">
                <p class="text-muted">Belum ada data penugasan Waka & Guru Piket.</p>
                <a href="{{ route('waka-kurikulum.jadwal.create') }}" class="btn btn-primary btn-sm">+ Buat Penugasan Baru</a>
            </div>
        @endif
    </div>
</div>
@endsection
