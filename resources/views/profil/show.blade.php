@extends('layouts.app')

@section('title', 'Profil Pengguna — Jurnal Sekolah')
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Profil {{ strtoupper($user->role) }}</h1>
        <p class="page-subtitle">Pengaturan Profil & Keamanan Akun Anda</p>
    </div>
</div>

<div class="grid-2">

    {{-- DATA PROFIL --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Profil</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('profil.update') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="nama">
                        Nama Lengkap <span class="req">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $user->nama) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">
                        Username <span class="req">*</span>
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="no_telp">
                        No WhatsApp / HP Aktif
                    </label>

                    <input
                        type="text"
                        id="no_telp"
                        name="no_telp"
                        value="{{ old('no_telp', $user->no_hp ?? ($user->guru->no_telp ?? '')) }}"
                        class="form-control"
                        placeholder="Contoh: 085707300240"
                    >
                    <small class="text-muted">Nomor ini digunakan sistem untuk mengirimkan notifikasi WhatsApp otomatis.</small>
                </div>
                @if($user->isGuru() && $user->guru)

                    <div class="form-group">
                        <label class="form-label">NIP</label>

                        <input
                            type="text"
                            value="{{ $user->guru->nip }}"
                            class="form-control"
                            readonly
                            style="background:var(--bg-card-header);"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bidang Studi</label>

                        <input
                            type="text"
                            value="{{ $user->guru->bidang_studi }}"
                            class="form-control"
                            readonly
                            style="background:var(--bg-card-header);"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="no_telp">
                            No Telepon
                        </label>

                        <input
                            type="text"
                            id="no_telp"
                            name="no_telp"
                            value="{{ old('no_telp', $user->guru->no_telp) }}"
                            class="form-control"
                        >
                    </div>

                {{-- PROFIL SISWA --}}
                @elseif($user->isSiswa() && $user->siswa)

                    <div class="form-group">
                        <label class="form-label">NISN</label>

                        <input
                            type="text"
                            value="{{ $user->siswa->nisn }}"
                            class="form-control"
                            readonly
                            style="background:var(--bg-card-header);"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kelas</label>

                        <input
                            type="text"
                            value="{{ $user->siswa->kelas->nama_kelas ?? '-' }}"
                            class="form-control"
                            readonly
                            style="background:var(--bg-card-header);"
                        >
                    </div>

                @endif

                <button type="submit" class="btn btn-primary mt-16">
                    SIMPAN PROFIL
                </button>

            </form>
        </div>
    </div>


    {{-- UBAH PASSWORD --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Keamanan / Ubah Password
            </h3>
        </div>

        <div class="card-body">
            <form action="{{ route('profil.password') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="password_lama">
                        Password Lama <span class="req">*</span>
                    </label>

                    <input
                        type="password"
                        id="password_lama"
                        name="password_lama"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_baru">
                        Password Baru <span class="req">*</span>
                    </label>

                    <input
                        type="password"
                        id="password_baru"
                        name="password_baru"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_baru_confirmation">
                        Konfirmasi Password Baru <span class="req">*</span>
                    </label>

                    <input
                        type="password"
                        id="password_baru_confirmation"
                        name="password_baru_confirmation"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-amber mt-16">
                    UBAH PASSWORD
                </button>

            </form>
        </div>
    </div>

</div>
@endsection