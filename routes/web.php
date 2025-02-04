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
Route::resource('kriteria-responden', App\Http\Controllers\KriteriaRespondenController::class)->middleware('auth');
Route::resource('wa-blast', App\Http\Controllers\WaBlastController::class)->middleware('auth');
Route::resource('single-sender', App\Http\Controllers\SingleSenderController::class)->middleware('auth');
Route::post('/update-session-status', [App\Http\Controllers\WaBlastController::class, 'updateSessionStatus'])->name('update.session.status');
Route::resource('indikator-dampak', App\Http\Controllers\IndikatorDampakController::class)->middleware('auth');
Route::resource('konversi', App\Http\Controllers\KonversiController::class)->middleware('auth');
Route::resource('pembuatan-project', App\Http\Controllers\PembuatanProjectController::class)->middleware('auth');
Route::get('/get-kaldik-data', [App\Http\Controllers\PembuatanProjectController::class, 'getKaldikData']);
Route::resource('project', App\Http\Controllers\ProjectController::class)->middleware('auth');

Route::prefix('project/kuesioner')->group(function () {
    Route::get('/show/{id}/{remark}', [App\Http\Controllers\ProjectController::class, 'showKuesioner'])->name('kuesioner.show');
    Route::post('/store', [App\Http\Controllers\ProjectController::class, 'tambahKuesioner'])->name('kuesioner.store');
    Route::get('/edit/{id}', [App\Http\Controllers\ProjectController::class, 'editKuesioner'])->name('kuesioner.edit');
    Route::post('/update/{id}', [App\Http\Controllers\ProjectController::class, 'saveKuesioner'])->name('kuesioner.update');
    Route::delete('/delete/{id}', [App\Http\Controllers\ProjectController::class, 'deleteKuesioner'])->name('kuesioner.delete');
});
