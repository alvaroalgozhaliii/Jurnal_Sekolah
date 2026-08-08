@extends('layouts.app')

@section('content')
<h2>Profil {{ strtoupper($user->role) }}</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
    <h3>Data Profil</h3>
    <form action="{{ route('profil.update') }}" method="POST">
        @csrf
        
        <p><label>Nama Lengkap:</label><br>
        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required style="width: 300px; padding: 5px;"></p>

        <p><label>Username:</label><br>
        <input type="text" name="username" value="{{ old('username', $user->username) }}" required style="width: 300px; padding: 5px;"></p>

        @if($user->isGuru() && $user->guru)
            <p><label>NIP:</label><br>
            <input type="text" value="{{ $user->guru->nip }}" readonly style="width: 300px; padding: 5px; background: #eee;"></p>

            <p><label>Bidang Studi:</label><br>
            <input type="text" value="{{ $user->guru->bidang_studi }}" readonly style="width: 300px; padding: 5px; background: #eee;"></p>

            <p><label>No Telepon:</label><br>
            <input type="text" name="no_telp" value="{{ old('no_telp', $user->guru->no_telp) }}" style="width: 300px; padding: 5px;"></p>
        @elseif($user->isSiswa() && $user->siswa)
            <p><label>NIS:</label><br>
            <input type="text" value="{{ $user->siswa->nis }}" readonly style="width: 300px; padding: 5px; background: #eee;"></p>

            <p><label>Kelas:</label><br>
            <input type="text" value="{{ $user->siswa->kelas->nama_kelas ?? '-' }}" readonly style="width: 300px; padding: 5px; background: #eee;"></p>
        @endif

        <button type="submit">SIMPAN PROFIL</button>
    </form>
</div>

<div style="border: 1px solid #ccc; padding: 15px;">
    <h3>Keamanan / Ubah Password</h3>
    <form action="{{ route('profil.password') }}" method="POST">
        @csrf

        <p><label>Password Lama:</label><br>
        <input type="password" name="password_lama" required style="width: 300px; padding: 5px;"></p>

        <p><label>Password Baru:</label><br>
        <input type="password" name="password_baru" required style="width: 300px; padding: 5px;"></p>

        <p><label>Konfirmasi Password Baru:</label><br>
        <input type="password" name="password_baru_confirmation" required style="width: 300px; padding: 5px;"></p>

        <button type="submit">UBAH PASSWORD</button>
    </form>
</div>
@endsection
