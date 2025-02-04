<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    UserController,
    RoleAndPermissionController,
    AspekController,
    IndikatorPersepsiController,
    PesanWaController,
    BobotAspekController,
    KriteriaRespondenController,
    WaBlastController,
    SingleSenderController,
    IndikatorDampakController,
    KonversiController,
    ProjectController,
    PembuatanProjectController
};

Route::get('/', fn () => view('welcome'));

Route::middleware(['auth', 'web'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', ProfileController::class)->name('profile');

    Route::resources([
        'users'               => UserController::class,
        'roles'               => RoleAndPermissionController::class,
        'aspek'               => AspekController::class,
        'indikator-persepsi'  => IndikatorPersepsiController::class,
        'pesan-wa'            => PesanWaController::class,
        'bobot-aspek'         => BobotAspekController::class,
        'kriteria-responden'  => KriteriaRespondenController::class,
        'wa-blast'            => WaBlastController::class,
        'single-sender'       => SingleSenderController::class,
        'indikator-dampak'    => IndikatorDampakController::class,
        'konversi'            => KonversiController::class,
        'project'             => ProjectController::class,
        'pembuatan-project'   => PembuatanProjectController::class,
    ]);

    Route::post('/update-session-status', [WaBlastController::class, 'updateSessionStatus'])
        ->name('update.session.status');

    Route::prefix('get-kaldik-data')->group(function () {
        Route::get('/', [PembuatanProjectController::class, 'getKaldikData'])->name('kaldik.index');
        Route::get('/detail/{kaldikID}', [PembuatanProjectController::class, 'getDetail'])->name('kaldik.detail');
    });

    Route::prefix('project/kuesioner')->name('kuesioner.')->group(function () {
        Route::get('/show/{id}/{remark}', [ProjectController::class, 'showKuesioner'])->name('show');
        Route::post('/store', [ProjectController::class, 'tambahKuesioner'])->name('store');
        Route::get('/edit/{id}', [ProjectController::class, 'editKuesioner'])->name('edit');
        Route::post('/update/{id}', [ProjectController::class, 'saveKuesioner'])->name('update');
        Route::delete('/delete/{id}', [ProjectController::class, 'deleteKuesioner'])->name('delete');
        Route::get('/show-responden/{id}', [ProjectController::class, 'showResponden'])->name('responden.show');
    });
});
