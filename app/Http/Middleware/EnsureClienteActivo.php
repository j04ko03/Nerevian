<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClienteActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ((int) $user->rol_id === 2) {
            $client = $user->clients;

            if (!$client || !$client->actiu) {
                return response()->json([
                    'error'   => 'client_pending',
                    'message' => 'El teu compte està pendent d\'activació per un operador.',
                ], 403);
            }
        }

        return $next($request);
    }
}