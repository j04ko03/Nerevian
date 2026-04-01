<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

// ANTIGUAMENTE CONOCIDO COMO KERNEL.PHP,
// AQUÍ SE DEFINE EL PUNTO DE ENTRADA DE TODA LA APP :).

return Application::configure(basePath: dirname(__DIR__))
    // LE DECIMOS A LARAVEL DONDE ESTÁN LOS ARCHIVOS CON RUTAS!
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // LO HE CREADO PARA MÁS ADELANTE POR EL TEMA DE MAIL.
        // BÁSICAMENTE PERMITE A LOS SERVICIOS EXTERNOS COMPROBAR QUE LA APP ESTÁ ACTIVA SIN TOCAR NUESTRA LÓGICA.
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ENCRIPTACION BACANA MENOS A LO QUE HE COMENTADO EN app/Http/Middleware/HandleAppearance.php 
        // Y app/Http/Middleware/HandleInertiaRequests.php 🗿
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // LE DÍ ALIAS A ADMIN PARA QUE EN ROUTES ESCRIBA ADMIN Y ALE SIN TENER QUE METER TODA LA RUTA 
        // LLAMANDO A app/Http/Middleware/EnsureIsAdmin.php.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
