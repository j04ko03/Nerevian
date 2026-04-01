<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

// SE ENCARGA DE COMO SE VE EL FRONT (TEMA VISUAL, OSCURO O CLARO, ETC)
// SI NO SE LE DICE NADA, SE PONE EN MODO SISTEMA.

// LA VARIABLE SE ENVIA A APP.BLADE.PHP. 
class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('appearance', $request->cookie('appearance') ?? 'system');

        return $next($request);
    }
}
