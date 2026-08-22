@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi — Jurnal Sekolah')
@section('page-title', 'Pengaturan Aplikasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan {{ strtoupper($role) }}</h1>
        <p class="page-subtitle">Konfigurasi & Preferensi Sistem Pengaturan Role</p>
    </div>
</div>

<div class="card" style="max-width:700px;">
@if($role === 'admin')
    <div class="card-header">
        <h3 class="card-title">Konfigurasi Sistem (Admin)</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pengaturan.update') }}" method="POST">
            @csrf
            
            <h4 class="text-navy mb-12">1. Hak Akses & Batas Waktu Pengisian Jurnal</h4>
            <div class="form-group">
                <label class="form-label" for="batas_waktu_jurnal_menit">Batas Waktu Pengisian Jurnal (Menit setelah KBM selesai)</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="batas_waktu_jurnal_menit" name="batas_waktu_jurnal_menit" value="{{ $batasWaktuJurnal }}" min="0" required class="form-control" style="width: 120px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:20px 0;">
            
            <h4 class="text-navy mb-12">2. Pengaturan Jam Pelajaran</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="jam_masuk">Jam Masuk Sekolah</label>
                    <input type="time" id="jam_masuk" name="jam_masuk" value="{{ $jamMasuk }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="jam_pulang">Jam Pulang Sekolah</label>
                    <input type="time" id="jam_pulang" name="jam_pulang" value="{{ $jamPulang }}" required class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="durasi_pelajaran_menit">Durasi per Jam Pelajaran (Menit)</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="durasi_pelajaran_menit" name="durasi_pelajaran_menit" value="{{ $durasiPelajaran }}" min="1" required class="form-control" style="width: 120px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-16">
                SIMPAN PENGATURAN ADMIN
            </button>
        </form>
    </div>
@elseif($role === 'guru')
    <div class="card-header">
        <h3 class="card-title">Notifikasi & Preferensi Guru</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('guru.pengaturan.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="d-flex align-center gap-8" style="cursor:pointer;">
                    <input type="checkbox" id="notif_jurnal" name="notif_jurnal" value="1" {{ $notifJurnal ? 'checked' : '' }} style="width:18px; height:18px;">
                    <span class="fw-bold">Aktifkan pengingat mengisi jurnal harian</span>
                </label>
            </div>
            <div class="form-group">
                <label class="d-flex align-center gap-8" style="cursor:pointer;">
                    <input type="checkbox" id="notif_presensi_masuk" name="notif_presensi_masuk" value="1" {{ $notifPresensiMasuk ? 'checked' : '' }} style="width:18px; height:18px;">
                    <span class="fw-bold">Aktifkan pengingat presensi masuk</span>
                </label>
            </div>
            <div class="form-group">
                <label class="d-flex align-center gap-8" style="cursor:pointer;">
                    <input type="checkbox" id="notif_presensi_keluar" name="notif_presensi_keluar" value="1" {{ $notifPresensiKeluar ? 'checked' : '' }} style="width:18px; height:18px;">
                    <span class="fw-bold">Aktifkan pengingat presensi keluar</span>
                </label>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:20px 0;">

            <div class="form-group">
                <label class="form-label" for="tema_tampilan">Pilihan Tema Tampilan</label>
                <select id="tema_tampilan" name="tema_tampilan" class="form-control" style="max-width:250px;">
                    <option value="light" {{ $temaTampilan == 'light' ? 'selected' : '' }}>Light Mode (Default Navy)</option>
                    <option value="dark" {{ $temaTampilan == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-16">
                SIMPAN PREFERENSI GURU
            </button>
        </form>
    </div>
@elseif($role === 'piket')
    <div class="card-header">
        <h3 class="card-title">Preferensi Petugas Piket</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('piket.pengaturan.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="toleransi_kelas_kosong_menit">Toleransi Peringatan Kelas Kosong (Menit setelah KBM dimulai)</label>
                <div class="d-flex align-center gap-8">
                    <input type="number" id="toleransi_kelas_kosong_menit" name="toleransi_kelas_kosong_menit" value="{{ $toleransiPiket }}" min="0" required class="form-control" style="width: 120px;">
                    <span class="text-muted fw-bold">menit</span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-16">
                SIMPAN PREFERENSI PIKET
            </button>
        </form>
    </div>
@elseif($role === 'siswa')
    <div class="card-header">
        <h3 class="card-title">Notifikasi & Preferensi Ortu / Siswa</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('siswa.pengaturan.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="d-flex align-center gap-8" style="cursor:pointer;">
                    <input type="checkbox" id="notif_jurnal" name="notif_jurnal" value="1" {{ $notifJurnal ? 'checked' : '' }} style="width:18px; height:18px;">
                    <span class="fw-bold">Aktifkan notifikasi jadwal & presensi</span>
                </label>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:20px 0;">

            <div class="form-group">
                <label class="form-label" for="tema_tampilan">Pilihan Tema Tampilan</label>
                <select id="tema_tampilan" name="tema_tampilan" class="form-control" style="max-width:250px;">
                    <option value="light" {{ $temaTampilan == 'light' ? 'selected' : '' }}>Light Mode (Default Navy)</option>
                    <option value="dark" {{ $temaTampilan == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-16">
                SIMPAN PREFERENSI SISWA
            </button>
        </form>
    </div>
@endif
</div>
@endsection
