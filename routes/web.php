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
    ProjectController,
    SettingController
};


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/', fn() => view('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/profile', ProfileController::class)->name('profile');

    Route::resources([
        'users' => UserController::class,
        'roles' => RoleAndPermissionController::class,
        'aspek' => AspekController::class,
        'indikator-persepsi' => IndikatorPersepsiController::class,
        'pesan-wa' => PesanWaController::class,
        'kriteria-responden' => KriteriaRespondenController::class,
        'wa-blast' => WaBlastController::class,
        'single-sender' => SingleSenderController::class,
        'indikator-dampak' => IndikatorDampakController::class,
        'konversi' => KonversiController::class,
        'project' => ProjectController::class,
        'pembuatan-project' => PembuatanProjectController::class,
        'setting' => SettingController::class,
    ]);

    Route::get('/bobot-aspek', [BobotAspekController::class, 'index'])->name('bobot-aspek.index');
    Route::put('/bobot-aspek', [BobotAspekController::class, 'update'])->name('bobot-aspek.update');

    Route::post('/update-session-status', [WaBlastController::class, 'updateSessionStatus'])->name('update.session.status');
    Route::prefix('get-kaldik-data')->controller(PembuatanProjectController::class)->group(function () {
        Route::get('/', 'getKaldikData')->name('kaldik.index');
        Route::get('/detail/{kaldikID}', 'getDetail')->name('kaldik.detail');
        Route::get('/peserta/{kaldikID}', 'getPeserta')->name('peserta.diklat');
    });

    Route::prefix('project/kuesioner')->controller(ProjectController::class)->group(function () {
        // Kuesioner
        Route::get('/show/{id}/{remark}', 'showKuesioner')->name('project.kuesioner.show');
        Route::post('/store', 'tambahKuesioner')->name('project.kuesioner.store');
        Route::get('/edit/{id}', 'editKuesioner')->name('project.kuesioner.edit');
        Route::post('/update/{id}', 'saveKuesioner')->name('project.kuesioner.update');
        Route::delete('/delete/{id}', 'deleteKuesioner')->name('project.kuesioner.delete');
        // Peserta
        Route::get('/show-responden/{id}', 'showResponden')->name('project.responden.show');
        //pesat wa
        Route::get('/show-pesan-wa/{id}', 'showPesanWa')->name('project.pesan.wa.show');
        //Bobot
        Route::get('/show-bobot/{id}', 'showBobot')->name('project.bobot.show');
        Route::get('/update-bobot/{id}', 'showBobot')->name('project.bobot.update');
    });
});
