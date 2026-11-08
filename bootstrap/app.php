<?php

use App\Http\Middleware\CabecerasSeguras;
use App\Http\Middleware\VerificarRol;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [CabecerasSeguras::class]);
        $middleware->alias(['rol' => VerificarRol::class]);
        $middleware->redirectGuestsTo('/ingresar');
        $middleware->redirectUsersTo('/panel');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['codigo_activacion', 'codigo_seguimiento']);
    })->create();
