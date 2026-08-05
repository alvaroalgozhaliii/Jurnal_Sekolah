<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;

Route::redirect('/', '/guru');

Route::resource('guru', GuruController::class);