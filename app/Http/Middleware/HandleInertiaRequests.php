<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

// LA FLECHA DE CUPIDO ENTRE LARAVEL Y VUE :) (TEMA COMUNICACIÓN).
// EN DICHA FLECHA SE DECIDE QUÉ INFORMACIÓN IMPORTANTE SE PUEDE 
// VER EN TODOS LOS COMPONENTES DE VUE. VAMOS, LOS DATOS GLOBALES.

// DE ESTA FORMA SE PUEDEN MINIMIZAR PETICIONES API.
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */

    // AQUÍ ESTAMOS DEJANDO QUE TODOS LOS COMPONENTES DE VUE 
    // PUEDAN VER EL NOMBRE DE LA APP Y EL NAME DEL USUARIO AUTH DEL MOMENTO.
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
