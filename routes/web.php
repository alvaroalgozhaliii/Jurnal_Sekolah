<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JadwalController;



Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('guru', GuruController::class);

Route::resource('kelas', KelasController::class);

Route::resource('siswa', SiswaController::class);

Route::resource('jadwal', JadwalController::class);