@extends('layouts.guest')

@section('title', 'Login Guru — Jurnal Sekolah')

@section('content')
<div class="login-card">
    <div class="login-header">
        <div class="login-logo">
            <svg class="svg-icon" viewBox="0 0 24 24" stroke-width="2" style="width: 32px; height: 32px; color: #ffffff;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
        </div>
        <div class="login-title">LOGIN GURU</div>
        <div class="login-subtitle">Jurnal Sekolah & Presensi KBM</div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-16">
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username Guru</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username guru" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password guru" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-16" style="width: 100%;">
            Masuk Portals Guru
        </button>
    </form>
</div>
@endsection