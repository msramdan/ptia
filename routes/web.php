<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
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
    DashboardController,
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
    HasilEvaluasiController,
    DataInterviewController,
    BackupController,
};

use App\Http\Controllers\Auth\VerifyOtpController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    try {
        // Ganti email tujuan dengan email lain yang bisa Anda akses untuk tes
        $tujuan = 'spotifyfarhan13@gmail.com';

        Mail::raw('Ini adalah isi email tes dari aplikasi Laravel Anda.', function ($message) use ($tujuan) {
            $message->to($tujuan)
                ->subject('Tes Koneksi Email SMTP');
        });

        return 'Berhasil mengirim email tes! Silakan periksa inbox (dan folder spam) di ' . $tujuan;
    } catch (\Exception $e) {
        // Tampilkan pesan error yang lebih detail
        return '<h1>Gagal Mengirim Email</h1><p>Pesan Error:</p><pre>' . $e->getMessage() . '</pre>';
    }
});

// CRON Notifikasi
Route::get('/kirim-notifikasi-alumni', [NotifikasiCronAlumniController::class, 'kirimNotifikasi']);
Route::get('/kirim-notifikasi-atasan', [NotifikasiCronAtasanController::class, 'kirimNotifikasi']);
Route::get('/auto-create-project', [AutoCreateProjectController::class, 'autoCreate']);
Route::get('/auto-insert-kuesiober-atasan', [AutoInsertKuesionerAtasanController::class, 'insertData']);

// Share kuesioner
Route::get('/responden-kuesioner/{id}/{target}', [RespondenKuesionerController::class, 'index'])->name('responden-kuesioner.index');
Route::get('/hasil-evaluasi-responden/{id}', [RespondenKuesionerController::class, 'hasilEvaluasi'])->name('hasil-evaluasi-responden.index');
Route::post('/responden-kuesioner', [RespondenKuesionerController::class, 'store'])->name('responden-kuesioner.store');

// OTP
Route::post('/verify-otp-modal', [VerifyOtpController::class, 'verify'])->name('otp.verify.modal');
Route::post('/resend-otp-modal', [VerifyOtpController::class, 'resend'])->name('otp.resend.modal');

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
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
        Route::get('/export-excel', 'exportExcel')->name('hasil-evaluasi.export-excel');
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

        // Update Deadline Responden selected
        Route::post('/responden/update-deadline-selected', 'updateDeadlineSelected')->name('update-deadline');

        // Kirim Notifikasi WhatsApp
        Route::post('/send-wa', 'sendNotifWa')->name('penyebaran-kuesioner.send.wa');

        // Log Pengiriman WhatsApp
        Route::get('/log-wa', 'getLogNotifWa')->name('penyebaran-kuesioner.log.wa');

        // Export-pdf
        Route::get('/{id}/export-penyebaran-kuesioner-pdf', 'exportPenyebaranKuesionerPdf')
            ->name('penyebaran-kuesioner.export-pdf');

        // BARU: Route untuk update status notifikasi responden
        Route::post('/responden/update-send-notif', 'updateSendNotifResponden')->name('penyebaran-kuesioner.update.send-notif-responden');

        // BARU: Route untuk update status notifikasi project
        Route::post('/project/update-send-notif', 'updateSendNotifProject')->name('penyebaran-kuesioner.update.send-notif-project');
    });


    // Route khusus untuk Data Sekunder
    Route::prefix('data-sekunder')->controller(DataSekunderController::class)->group(function () {
        Route::get('/', 'index')->name('data-sekunder.index');
        Route::post('/', 'store')->name('data-sekunder.store');
        Route::get('/get/{project_id}', 'getDataSekunder')->name('data-sekunder.get');
    });

    Route::prefix('data-interview')->name('data-interview.')->group(function () {
        Route::get('/', [DataInterviewController::class, 'index'])->name('index');

        // Route untuk halaman detail responden Alumni
        Route::get('/{project}/responden-alumni', [DataInterviewController::class, 'showRespondenAlumni'])->name('responden.alumni');

        // Route untuk halaman detail responden Atasan
        Route::get('/{project}/responden-atasan', [DataInterviewController::class, 'showRespondenAtasan'])->name('responden.atasan');

        // Route untuk menyimpan evidence (akan digunakan di halaman detail)
        Route::post('/responden/{responden}/alumni-evidence', [DataInterviewController::class, 'storeAlumniEvidence'])->name('storeAlumniEvidence');
        Route::post('/responden/{responden}/atasan-evidence', [DataInterviewController::class, 'storeAtasanEvidence'])->name('storeAtasanEvidence');
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
    Route::post('/wa-blast/update-aktif', [WaBlastController::class, 'updateAktif'])->name('wa-blast.update-aktif');
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
        // Export PDF
        Route::get('/{id}/export-pdf', 'exportPdf')->name('project.exportPdf');
    });


    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{fileName}', [BackupController::class, 'download'])->name('backup.download');
});
