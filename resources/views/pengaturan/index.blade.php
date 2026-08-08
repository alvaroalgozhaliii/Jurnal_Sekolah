@extends('layouts.app')

@section('content')
<h2>Pengaturan {{ strtoupper($role) }}</h2>

@if($role === 'admin')
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h3>1. Hak Akses & Batas Waktu Pengisian Jurnal</h3>
        <form action="{{ route('admin.pengaturan.update') }}" method="POST">
            @csrf
            <p>
                <label>Batas Waktu Pengisian Jurnal (Menit setelah mata pelajaran selesai):</label><br>
                <input type="number" name="batas_waktu_jurnal_menit" value="{{ $batasWaktuJurnal }}" min="0" required style="padding: 5px; width: 100px;"> menit
            </p>
            
            <h3>2. Pengaturan Jam Pelajaran</h3>
            <p>
                <label>Jam Masuk:</label><br>
                <input type="time" name="jam_masuk" value="{{ $jamMasuk }}" required style="padding: 5px;">
            </p>
            <p>
                <label>Jam Pulang:</label><br>
                <input type="time" name="jam_pulang" value="{{ $jamPulang }}" required style="padding: 5px;">
            </p>
            <p>
                <label>Durasi per Jam Pelajaran (Menit):</label><br>
                <input type="number" name="durasi_pelajaran_menit" value="{{ $durasiPelajaran }}" min="1" required style="padding: 5px; width: 100px;"> menit
            </p>

            <button type="submit">SIMPAN PENGATURAN ADMIN</button>
        </form>
    </div>
@elseif($role === 'guru')
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h3>Notifikasi & Preferensi Guru</h3>
        <form action="{{ route('guru.pengaturan.update') }}" method="POST">
            @csrf
            <p>
                <input type="checkbox" id="notif_jurnal" name="notif_jurnal" value="1" {{ $notifJurnal ? 'checked' : '' }}>
                <label for="notif_jurnal">Aktifkan pengingat mengisi jurnal harian</label>
            </p>
            <p>
                <input type="checkbox" id="notif_presensi_masuk" name="notif_presensi_masuk" value="1" {{ $notifPresensiMasuk ? 'checked' : '' }}>
                <label for="notif_presensi_masuk">Aktifkan pengingat presensi masuk</label>
            </p>
            <p>
                <input type="checkbox" id="notif_presensi_keluar" name="notif_presensi_keluar" value="1" {{ $notifPresensiKeluar ? 'checked' : '' }}>
                <label for="notif_presensi_keluar">Aktifkan pengingat presensi keluar</label>
            </p>

            <h3>Tampilan</h3>
            <p>
                <label>Pilihan Tema:</label><br>
                <select name="tema_tampilan" style="padding: 5px;">
                    <option value="light" {{ $temaTampilan == 'light' ? 'selected' : '' }}>Light Mode</option>
                    <option value="dark" {{ $temaTampilan == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                </select>
            </p>

            <button type="submit">SIMPAN PREFERENSI GURU</button>
        </form>
    </div>
@elseif($role === 'piket')
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h3>Preferensi Piket</h3>
        <form action="{{ route('piket.pengaturan.update') }}" method="POST">
            @csrf
            <p>
                <label>Toleransi Peringatan Kelas Kosong (Menit setelah jadwal dimulai):</label><br>
                <input type="number" name="toleransi_kelas_kosong_menit" value="{{ $toleransiPiket }}" min="0" required style="padding: 5px; width: 100px;"> menit
            </p>
            <button type="submit">SIMPAN PREFERENSI PIKET</button>
        </form>
    </div>
@elseif($role === 'siswa')
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h3>Notifikasi & Preferensi Siswa</h3>
        <form action="{{ route('siswa.pengaturan.update') }}" method="POST">
            @csrf
            <p>
                <input type="checkbox" id="notif_jurnal" name="notif_jurnal" value="1" {{ $notifJurnal ? 'checked' : '' }}>
                <label for="notif_jurnal">Aktifkan notifikasi jadwal & presensi</label>
            </p>

            <h3>Tampilan</h3>
            <p>
                <label>Pilihan Tema:</label><br>
                <select name="tema_tampilan" style="padding: 5px;">
                    <option value="light" {{ $temaTampilan == 'light' ? 'selected' : '' }}>Light Mode</option>
                    <option value="dark" {{ $temaTampilan == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                </select>
            </p>

            <button type="submit">SIMPAN PREFERENSI SISWA</button>
        </form>
    </div>
@endif
@endsection
