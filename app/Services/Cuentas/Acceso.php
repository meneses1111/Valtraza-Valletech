<?php

namespace App\Services\Cuentas;

use App\Models\Usuario;
use App\Support\EntradaFormulario;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class Acceso
{
    public function listarCuentas(): mixed
    {
        return Usuario::query()->select(['id_usuario', 'nombre_usuario', 'rol', 'activado', 'id_tecnico'])
            ->with('tecnico:id_tecnico,nombre')->orderBy('nombre_usuario')
            ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'cuentas_pagina')->withQueryString();
    }

    public function verificar(Usuario $usuario, string $contrasena): bool
    {
        if (! $usuario->activado || strlen($contrasena) > 72 || str_contains($contrasena, "\0")) {
            return false;
        }

        try {
            return Hash::check($contrasena, $usuario->getAuthPassword());
        } catch (RuntimeException) {
            return false;
        }
    }

    public function activar(string $codigo, string $contrasena): bool
    {
        for ($intento = 0; $intento < 3; $intento++) {
            try {
                return DB::transaction(function () use ($codigo, $contrasena): bool {
                    $usuario = Usuario::query()->where('codigo_activacion', $codigo)->lockForUpdate()->first();

                    if (! $usuario || $usuario->activado) {
                        return false;
                    }

                    $usuario->contrasena_hash = Hash::make($contrasena);
                    $usuario->sal = null;
                    $usuario->activado = true;
                    $usuario->codigo_activacion = Str::random(64);
                    $usuario->save();

                    return true;
                }, 3);
            } catch (QueryException $exception) {
                if (($exception->errorInfo[0] ?? null) !== '23505') {
                    throw $exception;
                }
            }
        }

        throw ValidationException::withMessages(['codigo_activacion' => 'No se pudo completar la activación. Intenta nuevamente.']);
    }

    public function crearCuentaTecnico(array $datos): string
    {
        for ($intento = 0; $intento < 3; $intento++) {
            try {
                return DB::transaction(function () use ($datos): string {
                    $codigo = Str::random(32);
                    $usuario = new Usuario([
                        'nombre_usuario' => $datos['nombre_usuario'],
                        'id_tecnico' => $datos['id_tecnico'],
                    ]);
                    $usuario->forceFill([
                        'rol' => 'tecnico',
                        'codigo_activacion' => $codigo,
                    ])->save();

                    return $codigo;
                }, 3);
            } catch (QueryException $exception) {
                $estado = $exception->errorInfo[0] ?? null;

                if ($estado === '23503') {
                    throw ValidationException::withMessages(['id_tecnico' => 'Selecciona un técnico registrado.']);
                }

                if ($estado !== '23505') {
                    throw $exception;
                }

                if (Usuario::query()->where('nombre_usuario', $datos['nombre_usuario'])->exists()) {
                    throw ValidationException::withMessages(['nombre_usuario' => 'Este nombre de usuario ya está registrado.']);
                }

                if (Usuario::query()->where('id_tecnico', $datos['id_tecnico'])->exists()) {
                    throw ValidationException::withMessages(['id_tecnico' => 'Este técnico ya tiene una cuenta.']);
                }
            }
        }

        throw ValidationException::withMessages(['registro' => 'No se pudo generar el código de activación. Intenta nuevamente.']);
    }
}
