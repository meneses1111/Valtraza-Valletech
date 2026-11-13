<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;

class LimitarSolicitudes extends ThrottleRequests
{
    protected function handleRequest($request, Closure $next, array $limits)
    {
        $claves = array_unique(array_map(fn ($limit) => $limit->key, $limits));
        sort($claves);
        $bloqueos = [];

        try {
            $almacen = Cache::store(config('cache.limiter') ?: config('cache.default'));
            $archivo = $almacen->getStore() instanceof FileStore;
            if ($archivo) {
                $claves = array_unique(array_map(fn ($clave) => substr(hash('sha256', $clave), 0, 2), $claves));
                sort($claves);
            }
            foreach ($claves as $clave) {
                if ($archivo) {
                    $bloqueos[] = $this->bloquearArchivo($clave);
                } else {
                    $bloqueo = $almacen->lock('valtraza-limiter:'.hash('sha256', $clave), 5);
                    $bloqueo->block(2);
                    $bloqueos[] = fn () => $bloqueo->release();
                }
            }
            foreach ($limits as $limit) {
                if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                    throw $this->buildException($request, $limit->key, $limit->maxAttempts, $limit->responseCallback);
                }
                if (! $limit->afterCallback) {
                    $this->limiter->hit($limit->key, $limit->decaySeconds);
                }
            }
        } catch (LockTimeoutException) {
            abort(429, '', ['Retry-After' => '1']);
        } finally {
            foreach (array_reverse($bloqueos) as $bloqueo) {
                $bloqueo();
            }
        }

        // El bloqueo solo protege el contador; no se retiene durante el controlador.
        $response = $next($request);
        foreach ($limits as $limit) {
            if ($limit->afterCallback && ($limit->afterCallback)($response)) {
                $this->limiter->hit($limit->key, $limit->decaySeconds);
            }
            $response = $this->addHeaders($response, $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts));
        }

        return $response;
    }

    private function bloquearArchivo(string $clave): Closure
    {
        // 256 archivos persistentes; nunca se elimina un mutex mientras otro proceso lo espera.
        $directorio = storage_path('framework/cache/limiter-locks');
        if (! is_dir($directorio) && ! @mkdir($directorio, 0770, true) && ! is_dir($directorio)) {
            abort(503, '', ['Retry-After' => '1']);
        }
        $archivo = @fopen($directorio.'/'.$clave.'.lock', 'c');
        if ($archivo === false) {
            abort(503, '', ['Retry-After' => '1']);
        }
        $limite = hrtime(true) + 2_000_000_000;
        while (! flock($archivo, LOCK_EX | LOCK_NB)) {
            if (hrtime(true) >= $limite) {
                fclose($archivo);
                throw new LockTimeoutException;
            }
            usleep(10000);
        }

        return function () use ($archivo): void {
            flock($archivo, LOCK_UN);
            fclose($archivo);
        };
    }
}
