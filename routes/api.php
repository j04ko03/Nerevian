<?php

use App\Http\Controllers\Admin\UsuariController;
use App\Http\Controllers\Admin\RegistreController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationRequestController;
use App\Http\Controllers\General\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas (sin token) ──────────────────────────────────
Route::post('/login',                 [AuthController::class, 'login']);
Route::post('/registration-requests', [RegistrationRequestController::class, 'store']);


// ── Rutas protegidas (requieren token Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Rutas administrativas (solo para rol_id = 1)
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('/users', UsuariController::class)->except(['store']);

        Route::get('/registration-requests',                [RegistreController::class, 'index']);
        Route::post('/registration-requests/{id}/approve', [RegistreController::class, 'approve']);
        Route::post('/registration-requests/{id}/reject',  [RegistreController::class, 'reject']);

        


    });
});