<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationRequestController;
use App\Http\Controllers\Auth\AdminPeticionsController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas (sin token) ──────────────────────────────────
Route::post('/login',                 [AuthController::class, 'login']);
Route::post('/registration-requests', [RegistrationRequestController::class, 'store']);

// ── Rutas protegidas (requieren token Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Rutas administrativas (solo para rol_id = 1)
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/registration-requests',               [AdminPeticionsController::class, 'index']);
        Route::post('/registration-requests/{id}/approve', [AdminPeticionsController::class, 'approve']);
        Route::post('/registration-requests/{id}/reject',  [AdminPeticionsController::class, 'reject']);
    });
});
