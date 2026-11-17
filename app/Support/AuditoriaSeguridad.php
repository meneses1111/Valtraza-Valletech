<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuditoriaSeguridad
{
    public static function registrar(string $evento, Request $request, array $contexto = [], bool $alerta = false): void
    {
        try {
            $usuario = $request->user()?->getAuthIdentifier();
        } catch (Throwable) {
            $usuario = null;
        }

        $datos = [
            'ip' => $request->ip(),
            'user_id' => $usuario,
            'ruta' => $request->route()?->getName(),
            ...$contexto,
        ];

        try {
            if ($alerta) {
                Log::alert('seguridad.'.$evento, $datos);
            } else {
                Log::warning('seguridad.'.$evento, $datos);
            }
        } catch (Throwable) {
            error_log('Valtraza: no se pudo escribir el evento de seguridad '.$evento);
        }
    }
}
