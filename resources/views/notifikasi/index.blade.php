@extends('layouts.app')

@section('title', 'Pusat Notifikasi — Jurnal Sekolah')
@section('page-title', 'Pusat Notifikasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pusat Notifikasi</h1>
        <p class="page-subtitle">Daftar semua notifikasi & pemberitahuan sistem</p>
    </div>
    @if($notifikasi->count() > 0)
    <div class="page-actions">
        <form action="{{ route('notifikasi.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary">Tandai Semua Dibaca</button>
        </form>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($notifikasi->count() > 0)
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:140px;">Waktu</th>
                        <th>Judul Notifikasi</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifikasi as $n)
                    <tr style="{{ !$n->dibaca ? 'background-color:rgba(217,119,6,.08); font-weight:600;' : '' }}">
                        <td class="text-muted" style="font-size:12px;">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                        <td class="fw-bold text-navy">{{ $n->judul }}</td>
                        <td>{{ $n->pesan }}</td>
                        <td>
                            @if($n->dibaca)
                                <span class="badge badge-gray">Sudah Dibaca</span>
                            @else
                                <span class="badge badge-amber">Belum Dibaca</span>
                            @endif
                        </td>
                        <td class="action-col">
                            @if($n->link)
                            <a href="{{ route('notifikasi.read', $n->id_notifikasi) }}" class="btn btn-primary btn-sm">Buka Detail</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Belum ada notifikasi.</div>
        </div>
        @endif
    </div>
</div>
@endsection
