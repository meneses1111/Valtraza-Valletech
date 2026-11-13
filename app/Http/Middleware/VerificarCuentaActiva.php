<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use App\Support\AuditoriaSeguridad;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarCuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            return $next($request);
        }

        $actual = $usuario->exists ? Usuario::query()->find($usuario->getAuthIdentifier()) : $usuario;

        if ($actual === null || ! $actual->activado) {
            AuditoriaSeguridad::registrar('sesion_revocada', $request);
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Inicia sesión nuevamente.'], 401)
                : redirect()->route('login')->withErrors(['nombre_usuario' => 'Inicia sesión nuevamente.']);
        }

        Auth::guard('web')->setUser($actual);

        return $next($request);
    }
}
