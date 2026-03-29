<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    //Protege rutas del dashboard verificando que exista el token JWT en sesión
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('api_token')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder al panel.');
        }

        return $next($request);
    }
}
