<?php

use App\Http\Controllers\Admin\UsuariController;
use App\Http\Controllers\Admin\RegistreController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegistrationRequestController;
use App\Http\Controllers\General\DashboardController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\SolicitudController;
use App\Http\Controllers\Api\OperacionController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Operador\ClientsController;
use App\Http\Controllers\Operador\SolicitudController as OperadorSolicitudController;
use App\Http\Controllers\Operador\OfertaController;
use App\Http\Controllers\Api\OfertaController as ClientOfertaController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas (sin token) ──────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registration-requests', [RegistrationRequestController::class, 'store']);

// ── Rutas protegidas (requieren token Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/search', [DashboardController::class, 'buscadorSolicitud']);

    // Rutas administrativas (solo para rol_id = 1)
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('/users', UsuariController::class)->except(['store']);
        Route::get('/registration-requests',                [RegistreController::class, 'index']);
        Route::post('/registration-requests/{id}/approve', [RegistreController::class, 'approve']);
        Route::post('/registration-requests/{id}/reject',  [RegistreController::class, 'reject']);
    });

    // Ruta para obtener los catálogos (lectura simple)
    Route::get('/catalogos', [CatalogoController::class, 'index']);

    // Rutas de client — bloquejades si actiu = false
    Route::middleware('client.actiu')->group(function () {
        Route::apiResource('solicitudes', SolicitudController::class);
        Route::post('/solicitudes/{solicitud}/ofertes/{oferta}/acceptar', [ClientOfertaController::class, 'acceptar']);
        Route::post('/solicitudes/{solicitud}/ofertes/{oferta}/rebutjar', [ClientOfertaController::class, 'rebutjar']);
        Route::post('/solicitudes/{solicitud}/contraoferta', [ClientOfertaController::class, 'contraoferta']);
        Route::apiResource('operaciones', OperacionController::class);
        Route::get('/tracking/{solicitud_id}', [TrackingController::class, 'show']);
        Route::get('/documentos/{id}/descargar', [DocumentController::class, 'download']);
        Route::apiResource('documentos', DocumentController::class);
    });

    // Rutas de operador
    Route::prefix('operador')->group(function () {
        Route::apiResource('clients', ClientsController::class);
        Route::get('solicituds', [OperadorSolicitudController::class, 'index']);
        Route::post('solicituds/{id}/oferta', [OfertaController::class, 'store']);
        Route::post('solicituds/{solicitud}/ofertes/{oferta}/acceptar', [OfertaController::class, 'acceptar']);
    });
});