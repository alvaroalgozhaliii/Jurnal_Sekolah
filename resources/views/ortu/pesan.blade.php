@extends('layouts.app')

@section('title', 'Pesan — Jurnal Sekolah')
@section('page-title', 'Pesan & Pemberitahuan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pesan & Pemberitahuan Ortu</h1>
        <p class="page-subtitle">Kotak Pesan dari Pihak Sekolah & Wali Kelas</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kotak Masuk Pesan</h3>
    </div>
    <div class="card-body">
        @if($pesanList->count() > 0)
        <ul style="list-style:none; padding:0;">
            @foreach($pesanList as $p)
            <li style="padding:16px 0; border-bottom:1px solid var(--border);">
                <div class="d-flex justify-between align-center mb-8">
                    <strong class="text-navy" style="font-size:15px;">{{ $p->judul }}</strong>
                    <span class="text-muted" style="font-size:12px;">{{ $p->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div style="font-size:14px; color:var(--text-primary);">{{ $p->pesan }}</div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="empty-state">
            <div class="empty-state-text">Tidak ada pesan masuk.</div>
        </div>
        @endif
    </div>
</div>
@endsection
