<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\PiketDashboardController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurnalHarianController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\PresensiGuruController;
use App\Http\Controllers\PresensiPiketController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\TahunPelajaranController;
use App\Http\Controllers\BackupController;

// ======================================================
// PUBLIC & AUTHENTICATION ROUTES
// ======================================================

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'piket' => redirect()->route('piket.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ======================================================
// AUTHENTICATED ROUTES FOR ALL ROLES
// ======================================================

Route::middleware(['auth'])->group(function () {

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::post('/profil/update', [ProfilController::class, 'updateProfil'])->name('profil.update');
    Route::post('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
});


// ======================================================
// ADMIN ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/rekap-kehadiran', [AdminDashboardController::class, 'rekapKehadiran'])->name('admin.rekap-kehadiran');

    // Pengguna (User Management)
    Route::resource('pengguna', PenggunaController::class);

    // Tahun Pelajaran
    Route::resource('tahun-pelajaran', TahunPelajaranController::class)->except(['create', 'show', 'edit', 'update']);
    Route::post('/tahun-pelajaran/{id}/aktif', [TahunPelajaranController::class, 'setAktif'])->name('tahun-pelajaran.set-aktif');

    // Backup & Restore
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::get('/backup/export', [BackupController::class, 'exportDatabase'])->name('admin.backup.export');
    Route::post('/backup/restore', [BackupController::class, 'restoreDatabase'])->name('admin.backup.restore');

    // Pengaturan Admin
    Route::post('/pengaturan', [PengaturanController::class, 'updateAdminSettings'])->name('admin.pengaturan.update');
});


// ======================================================
// GURU ROLE ROUTES (ADMIN & GURU)
// ======================================================

Route::middleware(['auth', 'role:admin,guru'])->prefix('guru-area')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
    Route::get('/presensi-saya', [PresensiGuruController::class, 'index'])->name('guru.presensi-saya');
    Route::post('/presensi-masuk', [PresensiGuruController::class, 'presensiMasuk'])->name('guru.presensi-masuk');
    Route::post('/presensi-keluar', [PresensiGuruController::class, 'presensiKeluar'])->name('guru.presensi-keluar');
    Route::post('/pengaturan', [PengaturanController::class, 'updateTeacherSettings'])->name('guru.pengaturan.update');
});


// ======================================================
// PIKET ROLE ROUTES (ADMIN & PIKET)
// ======================================================

Route::middleware(['auth', 'role:admin,piket'])->prefix('piket-area')->group(function () {
    Route::get('/dashboard', [PiketDashboardController::class, 'index'])->name('piket.dashboard');
    Route::get('/presensi', [PresensiPiketController::class, 'index'])->name('piket.presensi');
    Route::post('/presensi-guru', [PresensiPiketController::class, 'storeGuru'])->name('piket.presensi-guru.store');
    Route::post('/pengaturan', [PengaturanController::class, 'updatePiketSettings'])->name('piket.pengaturan.update');
});


// ======================================================
// SISWA ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:siswa'])->prefix('siswa-area')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('/jadwal-pelajaran', [SiswaDashboardController::class, 'jadwal'])->name('siswa.jadwal-pelajaran');
    Route::get('/presensi-saya', [SiswaDashboardController::class, 'presensi'])->name('siswa.presensi-saya');
    Route::get('/kelas-info', [SiswaDashboardController::class, 'kelas'])->name('siswa.kelas-info');
    Route::post('/pengaturan', [PengaturanController::class, 'updateSiswaSettings'])->name('siswa.pengaturan.update');
});


// ======================================================
// MASTER DATA & FEATURE ROUTES (ADMIN, GURU, PIKET)
// ======================================================

Route::middleware(['auth', 'role:admin,guru,piket'])->group(function () {

    // TRASH ROUTES (Must be defined BEFORE resource routes)
    Route::get('/guru/trash', [GuruController::class, 'trash'])->name('guru.trash');
    Route::put('/guru/{id}/restore', [GuruController::class, 'restore'])->name('guru.restore');
    Route::delete('/guru/{id}/force-delete', [GuruController::class, 'forceDelete'])->name('guru.forceDelete');

    Route::get('/siswa/trash', [SiswaController::class, 'trash'])->name('siswa.trash');
    Route::put('/siswa/{id}/restore', [SiswaController::class, 'restore'])->name('siswa.restore');
    Route::delete('/siswa/{id}/force-delete', [SiswaController::class, 'forceDelete'])->name('siswa.forceDelete');

    Route::get('/kelas/trash', [KelasController::class, 'trash'])->name('kelas.trash');
    Route::put('/kelas/{id}/restore', [KelasController::class, 'restore'])->name('kelas.restore');
    Route::delete('/kelas/{id}/force-delete', [KelasController::class, 'forceDelete'])->name('kelas.forceDelete');

    Route::get('/jurusan/trash', [JurusanController::class, 'trash'])->name('jurusan.trash');
    Route::put('/jurusan/{id}/restore', [JurusanController::class, 'restore'])->name('jurusan.restore');
    Route::delete('/jurusan/{id}/force-delete', [JurusanController::class, 'forceDelete'])->name('jurusan.forceDelete');

    Route::get('/jadwal/trash', [JadwalController::class, 'trash'])->name('jadwal.trash');
    Route::put('/jadwal/{id}/restore', [JadwalController::class, 'restore'])->name('jadwal.restore');
    Route::delete('/jadwal/{id}/force-delete', [JadwalController::class, 'forceDelete'])->name('jadwal.forceDelete');

    Route::get('/jurnal-harian/trash', [JurnalHarianController::class, 'trash'])->name('jurnal-harian.trash');
    Route::put('/jurnal-harian/{id}/restore', [JurnalHarianController::class, 'restore'])->name('jurnal-harian.restore');
    Route::delete('/jurnal-harian/{id}/force-delete', [JurnalHarianController::class, 'forceDelete'])->name('jurnal-harian.forceDelete');

    // RESOURCE ROUTES
    Route::resource('guru', GuruController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('jurusan', JurusanController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('jurnal-harian', JurnalHarianController::class);

    // ABSENSI SISWA BATCH
    Route::post('/absensi-siswa/batch', [AbsensiSiswaController::class, 'storeBatch'])->name('absensi-siswa.storeBatch');
    Route::resource('absensi-siswa', AbsensiSiswaController::class);
});