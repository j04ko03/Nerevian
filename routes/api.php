<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationRequestController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas (sin token) ──────────────────────────────────
Route::post('/login',                 [AuthController::class, 'login']);
Route::post('/registration-requests', [RegistrationRequestController::class, 'store']);

// ── Rutas protegidas (requieren token Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});
