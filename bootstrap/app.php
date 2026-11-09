<?php

use App\Http\Middleware\CabecerasSeguras;
use App\Http\Middleware\VerificarRol;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CabecerasSeguras::class);
        $middleware->alias(['rol' => VerificarRol::class]);
        $middleware->redirectGuestsTo('/ingresar');
        $middleware->redirectUsersTo('/panel');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['codigo_activacion', 'codigo_seguimiento']);
        $exceptions->report(function (QueryException $exception): bool {
            Log::error('Error de persistencia.', ['estado' => $exception->errorInfo[0] ?? $exception->getCode()]);

            return false;
        });
    })->create();
