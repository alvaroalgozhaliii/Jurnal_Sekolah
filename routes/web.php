<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\PiketDashboardController;
use App\Http\Controllers\OrtuDashboardController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\WakaDashboardController;
use App\Http\Controllers\KepalaSekolahController;
use App\Http\Controllers\SatpamController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\NotifikasiController;

use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MapelController;
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
            'ortu', 'siswa' => redirect()->route('ortu.dashboard'),
            'wali_kelas' => redirect()->route('walikelas.dashboard'),
            'waka_kesiswaan', 'waka_sdm' => redirect()->route('waka.dashboard'),
            'kepala_sekolah' => redirect()->route('kepala.dashboard'),
            'satpam' => redirect()->route('satpam.dashboard'),
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

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    // Pengajuan Izin / Dispensasi (Shared View & Create)
    Route::get('/pengajuan-izin', [PengajuanIzinController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan-izin/buat', [PengajuanIzinController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan-izin', [PengajuanIzinController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan-izin/{id}', [PengajuanIzinController::class, 'show'])->name('pengajuan.show');

    // Approvals
    Route::post('/pengajuan-izin/{id}/approve-piket', [PengajuanIzinController::class, 'approvePiket'])->name('pengajuan.approve.piket');
    Route::post('/pengajuan-izin/{id}/approve-waka', [PengajuanIzinController::class, 'approveWaka'])->name('pengajuan.approve.waka');
    Route::post('/pengajuan-izin/{id}/approve-kepala', [PengajuanIzinController::class, 'approveKepala'])->name('pengajuan.approve.kepala');
    Route::post('/pengajuan-izin/{id}/resend-wa', [PengajuanIzinController::class, 'resendWa'])->name('pengajuan.resend-wa');
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
// GURU ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,guru,wali_kelas'])->prefix('guru-area')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
    Route::get('/presensi-saya', [PresensiGuruController::class, 'index'])->name('guru.presensi-saya');
    Route::post('/presensi-masuk', [PresensiGuruController::class, 'presensiMasuk'])->name('guru.presensi-masuk');
    Route::post('/presensi-keluar', [PresensiGuruController::class, 'presensiKeluar'])->name('guru.presensi-keluar');
    Route::post('/pengaturan', [PengaturanController::class, 'updateTeacherSettings'])->name('guru.pengaturan.update');
});


// ======================================================
// PIKET ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,piket'])->prefix('piket-area')->group(function () {
    Route::get('/dashboard', [PiketDashboardController::class, 'index'])->name('piket.dashboard');
    Route::get('/presensi', [PresensiPiketController::class, 'index'])->name('piket.presensi');
    Route::post('/presensi-guru', [PresensiPiketController::class, 'storeGuru'])->name('piket.presensi-guru.store');
    Route::get('/anak-sakit', [PengajuanIzinController::class, 'anakSakitPiket'])->name('piket.anak-sakit');
    Route::post('/anak-sakit', [PengajuanIzinController::class, 'storeAnakSakitPiket'])->name('piket.anak-sakit.store');
    Route::post('/pengaturan', [PengaturanController::class, 'updatePiketSettings'])->name('piket.pengaturan.update');
});


// ======================================================
// ORTU / SISWA ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,ortu,siswa'])->prefix('ortu-area')->group(function () {
    Route::get('/dashboard', [OrtuDashboardController::class, 'index'])->name('ortu.dashboard');
    Route::get('/data-anak', [OrtuDashboardController::class, 'dataAnak'])->name('ortu.data-anak');
    Route::get('/jadwal-anak', [OrtuDashboardController::class, 'jadwal'])->name('ortu.jadwal-anak');
    Route::get('/presensi-anak', [OrtuDashboardController::class, 'presensi'])->name('ortu.presensi');
    Route::get('/rekap-bulanan', [OrtuDashboardController::class, 'rekapBulanan'])->name('ortu.rekap-bulanan');
    Route::get('/notifikasi-ortu', [OrtuDashboardController::class, 'notifikasi'])->name('ortu.notifikasi');
    Route::get('/pesan-ortu', [OrtuDashboardController::class, 'pesan'])->name('ortu.pesan');
    
    // Alias route names for compatibility
    Route::get('/jadwal-pelajaran', [OrtuDashboardController::class, 'jadwal'])->name('siswa.jadwal-pelajaran');
    Route::get('/presensi-saya', [OrtuDashboardController::class, 'presensi'])->name('siswa.presensi-saya');
    Route::get('/kelas-info', [OrtuDashboardController::class, 'dataAnak'])->name('siswa.kelas-info');
    Route::get('/siswa-dashboard', [OrtuDashboardController::class, 'index'])->name('siswa.dashboard');
});


// ======================================================
// WALI KELAS ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,guru,wali_kelas'])->prefix('walikelas-area')->group(function () {
    Route::get('/dashboard', [WaliKelasController::class, 'index'])->name('walikelas.dashboard');
    Route::get('/data-kelas', [WaliKelasController::class, 'dataKelas'])->name('walikelas.data-kelas');
    Route::get('/rekap-presensi', [WaliKelasController::class, 'rekapPresensi'])->name('walikelas.rekap-presensi');
    Route::get('/jurnal', [WaliKelasController::class, 'jurnal'])->name('walikelas.jurnal');
});


// ======================================================
// WAKA ROLE ROUTES (KESISWAAN & SDM)
// ======================================================

Route::middleware(['auth', 'role:admin,waka_kesiswaan,waka_sdm'])->prefix('waka-area')->group(function () {
    Route::get('/dashboard', [WakaDashboardController::class, 'index'])->name('waka.dashboard');
    Route::get('/persetujuan', [WakaDashboardController::class, 'daftarPersetujuan'])->name('waka.persetujuan.index');
    Route::get('/persetujuan/{id}', [WakaDashboardController::class, 'show'])->name('waka.persetujuan.show');
    Route::post('/persetujuan/{id}/proses', [WakaDashboardController::class, 'prosesKeputusan'])->name('waka.persetujuan.proses');
});


// ======================================================
// KEPALA SEKOLAH ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,kepala_sekolah'])->prefix('kepala-area')->group(function () {
    Route::get('/dashboard', [KepalaSekolahController::class, 'index'])->name('kepala.dashboard');
});


// ======================================================
// SATPAM ROLE ROUTES
// ======================================================

Route::middleware(['auth', 'role:admin,satpam'])->prefix('satpam-area')->group(function () {
    Route::get('/dashboard', [SatpamController::class, 'index'])->name('satpam.dashboard');
    Route::get('/periksa/{id}', [SatpamController::class, 'show'])->name('satpam.show');
    Route::post('/verifikasi/{id}', [SatpamController::class, 'verifikasi'])->name('satpam.verifikasi');
});


// ======================================================
// MASTER DATA & FEATURE ROUTES (ADMIN, GURU, PIKET, WALI KELAS)
// ======================================================

Route::middleware(['auth', 'role:admin,guru,piket,wali_kelas,waka_kesiswaan,waka_sdm,kepala_sekolah'])->group(function () {

    // TRASH ROUTES
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

    Route::get('/mapel/trash', [MapelController::class, 'trash'])->name('mapel.trash');
    Route::put('/mapel/{id}/restore', [MapelController::class, 'restore'])->name('mapel.restore');
    Route::delete('/mapel/{id}/force-delete', [MapelController::class, 'forceDelete'])->name('mapel.forceDelete');

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
    Route::resource('mapel', MapelController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('jurnal-harian', JurnalHarianController::class);

    // ABSENSI SISWA BATCH
    Route::post('/absensi-siswa/batch', [AbsensiSiswaController::class, 'storeBatch'])->name('absensi-siswa.storeBatch');
    Route::resource('absensi-siswa', AbsensiSiswaController::class);
});