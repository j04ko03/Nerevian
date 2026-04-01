<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


// PA LA CALLE SI NO ESTÁ AUTH ALGUIEN
class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Verifies if the user is authenticated.
     * Redirects to login if not authenticated.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
