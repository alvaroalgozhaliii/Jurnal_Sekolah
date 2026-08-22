@extends('layouts.guest')

@section('title', 'Login Ortu / Siswa — Jurnal Sekolah')

@section('content')
<div class="login-card">
    <div class="login-header">
        <div class="login-logo">
            <svg class="svg-icon" viewBox="0 0 24 24" stroke-width="2" style="width: 32px; height: 32px; color: #ffffff;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
        </div>
        <div class="login-title">PORTAL ORANG TUA / SISWA</div>
        <div class="login-subtitle">Jurnal Sekolah & Informasi Presensi</div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-16">
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username / NIS</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username / NIS" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-16" style="width: 100%;">
            Masuk Portal
        </button>
    </form>
</div>
@endsection