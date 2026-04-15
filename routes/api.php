<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SuratMasukController;
use App\Http\Controllers\Api\V1\SuratKeluarController;
use App\Http\Controllers\Api\V1\DisposisiController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\TemplateSuratController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Roles
        Route::get('/roles', function() {
            return response()->json(\App\Models\Role::all());
        });

        // Users
        Route::apiResource('users', UserController::class);

        // Surat Masuk
        Route::get('surat-masuk/generate-number', [SuratMasukController::class, 'generateNumber']);
        Route::apiResource('surat-masuk', SuratMasukController::class);

        // Surat Keluar
        Route::get('surat-keluar/generate-number', [SuratKeluarController::class, 'generateNumber']);
        Route::post('surat-keluar/{id}/sign', [SuratKeluarController::class, 'sign']);
        Route::apiResource('surat-keluar', SuratKeluarController::class);

        // Disposisi
        Route::get('disposisi-all', [DisposisiController::class, 'all']);
        Route::get('disposisi/{surat_id}', [DisposisiController::class, 'index']);
        Route::post('disposisi', [DisposisiController::class, 'store']);
        Route::put('disposisi/{id}/status', [DisposisiController::class, 'updateStatus']);
        Route::post('disposisi/{id}/sign', [DisposisiController::class, 'sign']);

        // Template Surat
        Route::get('template-surat/{id}/preview', [TemplateSuratController::class, 'preview']);
        Route::apiResource('template-surat', TemplateSuratController::class);
    });
});
