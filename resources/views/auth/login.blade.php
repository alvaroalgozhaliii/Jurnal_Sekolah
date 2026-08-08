@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 40px auto; border: 1px solid #ccc; padding: 20px;">
    <h2>Login Jurnal_Sekolah</h2>

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf

        <div style="margin-bottom: 15px;">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;">
        </div>

        <div>
            <button type="submit" style="width: 100%; padding: 10px;">LOGIN</button>
        </div>
    </form>
</div>
@endsection
