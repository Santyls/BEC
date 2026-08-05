<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'bec.auth' => \App\Http\Middleware\BecAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Red de seguridad: si un GET a BEC_API falla y el controlador no lo capturó
        // explícitamente, mostramos un aviso claro en vez del 500 crudo de Laravel.
        $exceptions->render(function (\App\Exceptions\BecApiException $e, $request) {
            if ($e->status === 401) {
                return redirect()->route('login')->with('error', 'Tu sesión expiró, inicia sesión de nuevo.');
            }
            return back()->with('error', $e->mensajeUsuario());
        });
    })->create();
