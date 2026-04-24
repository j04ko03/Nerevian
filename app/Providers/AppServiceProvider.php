<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

// 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. AÑADE ESTE BLOQUE AQUÍ
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // PRÁCTICA COMÚN PARA EVITAR DOLORES DE CABEZA SI ALGUIEN INTENTA HACER UN COMANDO PELIGROSO EN PROD.
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // REGLAS PARA EVITAR QUE:
        Password::defaults(
            // EN PROD LA GENTE META COSAS RARAS (HACK STUFF) Y QUE MEJORE SU CONTRA.
            fn(): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                // SOLO ESTO TIENE QUE VER CON EL HACK STUFF. CONSULTA LA API 
                // HAVEIBEENPWNED QUE ES BÁSICAMENTE PARA VERIFICAR QUE LA 
                // CONTRA INGRESADA NO SALE EN NINGUNA FILTRACION. SI ES EL 
                // CASO DENIEGA LA CONTRA QUE EL USUARIO QUIERE CREAR 
                // (ASÍ EVITAMOS QUE CON ESA CONTRA UN RANDOM ROBE SU CUENTA).
                ->uncompromised()
            // EN DESARROLLO NO HAY REGLAS
            : null
        );
    }
}
