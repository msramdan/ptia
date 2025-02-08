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

    Route::prefix('project')->controller(ProjectController::class)->group(function () {
        // Kuesioner
        Route::get('/kuesioner/show/{id}/{remark}', 'showKuesioner')->name('project.kuesioner.show');
        Route::post('/kuesioner/store', 'tambahKuesioner')->name('project.kuesioner.store');
        Route::get('/kuesioner/edit/{id}', 'editKuesioner')->name('project.kuesioner.edit');
        Route::post('/kuesioner/update/{id}', 'saveKuesioner')->name('project.kuesioner.update');
        Route::delete('/kuesioner/delete/{id}', 'deleteKuesioner')->name('project.kuesioner.delete');
        // Peserta
        Route::get('/peserta/show/{id}', 'showPeserta')->name('project.peserta.show');
        //pesat wa
        Route::get('/pesan-wa/show/{id}', 'showPesanWa')->name('project.pesan.wa.show');
        Route::put('/pesan-wa/update/{id}', 'updatePesanWa')->name('project.pesan.wa.update');
        //Bobot
        Route::get('/bobot/show/{id}', 'showBobot')->name('project.bobot.show');
        Route::put('/bobot/update', 'updateBobot')->name('project.bobot.update');
    });
});
