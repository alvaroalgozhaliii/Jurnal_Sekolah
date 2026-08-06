<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurnalHarianController;



Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');



// =======================
// RESOURCE
// =======================

Route::resource('guru', GuruController::class);

Route::resource('kelas', KelasController::class);

Route::resource('siswa', SiswaController::class);

Route::resource('jadwal', JadwalController::class);




// =======================
// JURNAL HARIAN
// =======================

// Trash
Route::get('/jurnal-harian/trash', [JurnalHarianController::class, 'trash'])
    ->name('jurnal-harian.trash');

Route::put('/jurnal-harian/{id}/restore', [JurnalHarianController::class, 'restore'])
    ->name('jurnal-harian.restore');

Route::delete('/jurnal-harian/{id}/force-delete', [JurnalHarianController::class, 'forceDelete'])
    ->name('jurnal-harian.forceDelete');


// Resource jurnal
Route::resource('jurnal-harian', JurnalHarianController::class);





// =======================
// GURU TRASH
// =======================

Route::get('/guru/trash', [GuruController::class, 'trash'])
    ->name('guru.trash');

Route::put('/guru/{id}/restore', [GuruController::class, 'restore'])
    ->name('guru.restore');

Route::delete('/guru/{id}/force-delete', [GuruController::class, 'forceDelete'])
    ->name('guru.forceDelete');




// =======================
// SISWA TRASH
// =======================

Route::get('/siswa/trash', [SiswaController::class, 'trash'])
    ->name('siswa.trash');

Route::put('/siswa/{id}/restore', [SiswaController::class, 'restore'])
    ->name('siswa.restore');

Route::delete('/siswa/{id}/force-delete', [SiswaController::class, 'forceDelete'])
    ->name('siswa.forceDelete');




// =======================
// KELAS TRASH
// =======================

Route::get('/kelas/trash', [KelasController::class, 'trash'])
    ->name('kelas.trash');

Route::put('/kelas/{id}/restore', [KelasController::class, 'restore'])
    ->name('kelas.restore');

Route::delete('/kelas/{id}/force-delete', [KelasController::class, 'forceDelete'])
    ->name('kelas.forceDelete');





// =======================
// JADWAL TRASH
// =======================

Route::get('/jadwal/trash', [JadwalController::class, 'trash'])
    ->name('jadwal.trash');

Route::put('/jadwal/{id}/restore', [JadwalController::class, 'restore'])
    ->name('jadwal.restore');

Route::delete('/jadwal/{id}/force-delete', [JadwalController::class, 'forceDelete'])
    ->name('jadwal.forceDelete');