<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cron\{
    AutoCreateProjectController,
    NotifikasiCronAlumniController,
    NotifikasiCronAtasanController,
    AutoInsertKuesionerAtasanController
};
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
    KuesionerController,
    PembuatanProjectController,
    PengumpulanDataController,
    PenyebaranKuesionerController,
    ProjectController,
    RespondenKuesionerController,
    SettingController,
    DataSekunderController,
    HasilEvaluasiController
};

Route::get('/', function () {
    return view('welcome');
});

// CRON Notifikasi
Route::get('/kirim-notifikasi-alumni', [NotifikasiCronAlumniController::class, 'kirimNotifikasi']);
Route::get('/kirim-notifikasi-atasan', [NotifikasiCronAtasanController::class, 'kirimNotifikasi']);
Route::get('/auto-create-project', [AutoCreateProjectController::class, 'autoCreate']);
Route::get('/auto-insert-kuesiober-atasan', [AutoInsertKuesionerAtasanController::class, 'insertData']);

// Share kuesioner
Route::get('/responden-kuesioner/{id}/{target}', [RespondenKuesionerController::class, 'index'])->name('responden-kuesioner.index');
Route::post('/responden-kuesioner', [RespondenKuesionerController::class, 'store'])->name('responden-kuesioner.store');

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
        'kuesioner' => KuesionerController::class,
    ]);

    // Route khusus untuk Hasil Evaluasi
    Route::prefix('hasil-evaluasi')->controller(HasilEvaluasiController::class)->group(function () {
        Route::get('/', 'index')->name('hasil-evaluasi.index');
        Route::get('/level-3/{id}', 'showLevel3')->name('hasil-evaluasi.detail-skor.level3');
        Route::get('/level-4/{id}', 'showLevel4')->name('hasil-evaluasi.detail-skor.level4');
        Route::get('/detail-level-3', 'getDetailSkorLevel3')->name('detail-level-3.responden');
        Route::get('/detail-level-4', 'getDetailSkorLevel4')->name('detail-level-4.responden');
    });

    Route::prefix('penyebaran-kuesioner')->controller(PenyebaranKuesionerController::class)->group(function () {
        Route::get('/', 'index')->name('penyebaran-kuesioner.index');

        // Responden Alumni
        Route::get('/responden-alumni/show/{id}', 'showRespondenAlumni')->name('penyebaran-kuesioner.responden-alumni.show');

        // Responden Atasan
        Route::get('/responden-atasan/show/{id}', 'showRespondenAtasan')->name('penyebaran-kuesioner.responden-atasan.show');

        // Update Telepon Responden
        Route::post('/responden/update-telepon', 'updateTelepon')->name('penyebaran-kuesioner.update.telepon');

        // Update Deadline Responden
        Route::post('/responden/update-deadline', 'updateDeadline')->name('penyebaran-kuesioner.update.deadline');

        // Kirim Notifikasi WhatsApp
        Route::post('/send-wa', 'sendNotifWa')->name('penyebaran-kuesioner.send.wa');

        // Log Pengiriman WhatsApp
        Route::get('/log-wa', 'getLogNotifWa')->name('penyebaran-kuesioner.log.wa');
    });

    // Route khusus untuk Data Sekunder
    Route::prefix('data-sekunder')->controller(DataSekunderController::class)->group(function () {
        Route::get('/', 'index')->name('data-sekunder.index');
        Route::post('/', 'store')->name('data-sekunder.store');
        Route::get('/get/{project_id}', 'getDataSekunder')->name('data-sekunder.get');
    });

    Route::prefix('pengumpulan-data')->controller(PengumpulanDataController::class)->group(function () {
        Route::get('/', 'index')->name('pengumpulan-data.index');
        // Rekap Kuesioner
        Route::get('/rekap-kuesioner/{id}/{remark}', 'rekapKuesioner')->name('penyebaran-kuesioner.rekap.kuesioner');
        // Ekspor Excel
        Route::get('/export-rekap-kuesioner/{id}/{remark}', 'exportExcel')->name('pengumpulan-data.export-excel');
    });

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
        Route::post('/kuesioner/store', 'storeKuesioner')->name('project.kuesioner.store');
        Route::get('/kuesioner/edit/{id}', 'editKuesioner')->name('project.kuesioner.edit');
        Route::post('/kuesioner/update/{id}', 'updateKuesioner')->name('project.kuesioner.update');
        Route::delete('/kuesioner/delete/{id}', 'deleteKuesioner')->name('project.kuesioner.delete');
        // responden
        Route::get('/responden/show/{id}', 'showResponden')->name('project.responden.show');
        Route::put('/responden/update/{id}', 'updateResponden')->name('project.responden.update');
        //pesat wa
        Route::get('/pesan-wa/show/{id}', 'showPesanWa')->name('project.pesan.wa.show');
        Route::put('/pesan-wa/update/{id}', 'updatePesanWa')->name('project.pesan.wa.update');
        //Bobot
        Route::get('/bobot/show/{id}', 'showBobot')->name('project.bobot.show');
        Route::put('/bobot/update', 'updateBobot')->name('project.bobot.update');
        // Update Status
        Route::put('/update-status/{id}', 'updateStatus')->name('project.updateStatus');
    });
});
