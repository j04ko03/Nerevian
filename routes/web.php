<?php

use Illuminate\Support\Facades\Route;

// Todas las rutas del frontend se manejan por el SPA de Vue.
// Laravel solo sirve la plantilla Blade y Vue Router se encarga del routing.
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
