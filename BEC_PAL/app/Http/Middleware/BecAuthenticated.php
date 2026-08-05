<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * No usa el sistema de auth de Laravel (no hay tabla `users` local): la sesión
 * "autenticada" es simplemente tener un access_token de BEC_API guardado.
 */
class BecAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('bec_access_token')) {
            return redirect()->route('login')->with('error', 'Tu sesión expiró, inicia sesión de nuevo.');
        }

        return $next($request);
    }
}
