@extends('layouts.app')

@section('title', 'Dashboard — Jurnal Sekolah')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang di Jurnal Sekolah</h1>
        <p class="page-subtitle">Sistem Informasi Manajemen Presensi & Kegiatan Belajar Mengajar</p>
    </div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3 class="card-title">Informasi Akun Anda</h3>
    </div>
    <div class="card-body">
        <p class="mb-8">Nama: <strong>{{ Auth::user()->nama }}</strong></p>
        <p class="mb-8">Role: <strong><span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}</span></strong></p>
        <p>Username: <strong>{{ Auth::user()->username }}</strong></p>
    </div>
</div>
@endsection