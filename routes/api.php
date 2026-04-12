<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationRequestController;
use App\Http\Controllers\Auth\AdminPeticionsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\SolicitudController;
use App\Http\Controllers\Api\OperacionController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\DocumentController;

// ── Rutas públicas (sin token) ──────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registration-requests', [RegistrationRequestController::class, 'store']);

// ── Rutas protegidas (requieren token Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Rutas administrativas (solo para rol_id = 1)
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/registration-requests', [AdminPeticionsController::class, 'index']);
        Route::post('/registration-requests/{id}/approve', [AdminPeticionsController::class, 'approve']);
        Route::post('/registration-requests/{id}/reject', [AdminPeticionsController::class, 'reject']);
    });

    // Ruta para obtener los catálogos (lectura simple)
    Route::get('/catalogos', [CatalogoController::class, 'index']);

    // CRUD de solicitudes (por eso apiResource (crea automaticamente los GET POST PUT DELETE correspondientes, vamos, el REST, en las rutas. Así que no tenemos que escribir esas rutas blablablabla))
    Route::apiResource('solicitudes', SolicitudController::class);

    // CRUD de operaciones/ecnargos/pedidos, como sea que lo llamemos.
    Route::apiResource('operaciones', OperacionController::class);

    // Ruta para obtener el tracking de una solicitud específica.
    Route::get('/tracking/{solicitud_id}', [TrackingController::class, 'show']);

    // Ruta para descargar un documento específico. 
    Route::get('/documentos/{id}/descargar', [DocumentController::class, 'download']);

    // CRUD de documentitos
    Route::apiResource('documentos', DocumentController::class);

});
