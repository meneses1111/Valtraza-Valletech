<?php

use App\Http\Middleware\CabecerasSeguras;
use App\Http\Middleware\LimitarSolicitudes;
use App\Http\Middleware\VerificarCuentaActiva;
use App\Http\Middleware\VerificarRol;
use App\Support\AuditoriaSeguridad;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CabecerasSeguras::class);
        $middleware->appendToGroup('web', VerificarCuentaActiva::class);
        $middleware->alias(['rol' => VerificarRol::class, 'throttle' => LimitarSolicitudes::class]);
        $middleware->redirectGuestsTo('/ingresar');
        $middleware->redirectUsersTo('/panel');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['codigo_activacion', 'codigo_seguimiento']);
        $exceptions->report(function (QueryException $exception): bool {
            Log::error('Error de persistencia.', ['estado' => $exception->errorInfo[0] ?? $exception->getCode()]);

            return false;
        });
        $exceptions->render(function (QueryException $exception, Request $request) {
            $estado = (string) ($exception->errorInfo[0] ?? $exception->getCode());

            if (str_starts_with($estado, '08') || in_array($estado, ['57P01', '57P02', '57P03', '57014', '55P03', '53300'], true)) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Servicio temporalmente no disponible.'], 503, ['Retry-After' => '30'])
                    : response()->view('errors.503', [], 503, ['Retry-After' => '30']);
            }

            return null;
        });
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request): Response {
            if (in_array($response->getStatusCode(), [403, 429], true)) {
                AuditoriaSeguridad::registrar($response->getStatusCode() === 403 ? 'acceso_denegado' : 'limite_excedido', $request);
            }

            return CabecerasSeguras::aplicar($response, $request);
        });
    })->create();
