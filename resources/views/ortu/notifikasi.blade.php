@extends('layouts.app')

@section('title', 'Notifikasi Ortu — Jurnal Sekolah')
@section('page-title', 'Notifikasi Ortu')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Notifikasi Orang Tua</h1>
        <p class="page-subtitle">Daftar pemberitahuan aktivitas & kehadiran anak</p>
    </div>
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
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifikasi as $n)
                    <tr style="{{ !$n->dibaca ? 'background-color:rgba(217,119,6,.08); font-weight:600;' : '' }}">
                        <td class="text-muted" style="font-size:12px;">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                        <td class="fw-bold text-navy">{{ $n->judul }}</td>
                        <td>{{ $n->pesan }}</td>
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
            <div class="empty-state-text">Tidak ada notifikasi.</div>
        </div>
        @endif
    </div>
</div>
@endsection
