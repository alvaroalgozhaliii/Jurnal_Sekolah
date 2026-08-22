@extends('layouts.guest')

@section('title', 'Login — Jurnal Sekolah')

@section('content')
<div class="login-card">
    <div class="login-header">
        <div class="login-logo">
            <svg class="svg-icon" viewBox="0 0 24 24" stroke-width="2" style="width: 32px; height: 32px; color: #ffffff;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
        <div class="login-title">JURNAL SEKOLAH</div>
        <div class="login-subtitle">Sistem Informasi Presensi KBM</div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-16">
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-16">
            <div>Username atau password salah.</div>
        </div>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Masukkan username" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-16" style="width: 100%;">
            Masuk Sistem
        </button>
    </form>
</div>
@endsection
