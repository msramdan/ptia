<?php

use App\Http\Controllers\Api\HasilEvaluasiController as ApiHasilEvaluasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hasil-evaluasi/{kaldikID}', [ApiHasilEvaluasiController::class, 'getByKaldikID']);

// Fallback untuk API
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'data' => null
    ], 404);
});
