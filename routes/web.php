<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/', fn () => view('dashboard'));
    Route::get('/dashboard', fn () => view('dashboard'));
    Route::get('/profile', App\Http\Controllers\ProfileController::class)->name('profile');
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleAndPermissionController::class);
});

Route::resource('aspek', App\Http\Controllers\AspekController::class)->middleware('auth');
Route::resource('indikator-persepsi', App\Http\Controllers\IndikatorPersepsiController::class)->middleware('auth');
Route::resource('pesan-wa', App\Http\Controllers\PesanWaController::class)->middleware('auth');
Route::resource('bobot-aspek', App\Http\Controllers\BobotAspekController::class)->middleware('auth');
