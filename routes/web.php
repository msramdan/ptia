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
    PembuatanProjectController,
    ProjectController
};


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/', fn () => view('dashboard'));
    Route::get('/dashboard', fn () => view('dashboard'));
    Route::get('/profile', ProfileController::class)->name('profile');

    Route::resources([
        'users' => UserController::class,
        'roles' => RoleAndPermissionController::class,
        'aspek' => AspekController::class,
        'indikator-persepsi' => IndikatorPersepsiController::class,
        'pesan-wa' => PesanWaController::class,
        'bobot-aspek' => BobotAspekController::class,
        'kriteria-responden' => KriteriaRespondenController::class,
        'wa-blast' => WaBlastController::class,
        'single-sender' => SingleSenderController::class,
        'indikator-dampak' => IndikatorDampakController::class,
        'konversi' => KonversiController::class,
        'project' => ProjectController::class,
        'pembuatan-project' => PembuatanProjectController::class,
    ]);

    Route::post('/update-session-status', [WaBlastController::class, 'updateSessionStatus'])->name('update.session.status');
    Route::prefix('get-kaldik-data')->controller(PembuatanProjectController::class)->group(function () {
        Route::get('/', 'getKaldikData')->name('kaldik.index');
        Route::get('/detail/{kaldikID}', 'getDetail')->name('kaldik.detail');
    });

    Route::prefix('project/kuesioner')->controller(ProjectController::class)->group(function () {
        // Kuesioner
        Route::get('/show/{id}/{remark}', 'showKuesioner')->name('kuesioner.show');
        Route::post('/store', 'tambahKuesioner')->name('kuesioner.store');
        Route::get('/edit/{id}', 'editKuesioner')->name('kuesioner.edit');
        Route::post('/update/{id}', 'saveKuesioner')->name('kuesioner.update');
        Route::delete('/delete/{id}', 'deleteKuesioner')->name('kuesioner.delete');
        // Peserta
        Route::get('/show-responden/{id}', 'showResponden')->name('responden.show');
    });

});
