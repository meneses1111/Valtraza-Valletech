<?php

namespace App\Services\Cuentas;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Acceso
{
    public function activar(string $codigo, string $contrasena): bool
    {
        return DB::transaction(function () use ($codigo, $contrasena): bool {
            $usuario = Usuario::query()->where('codigo_activacion', $codigo)->lockForUpdate()->first();

            if (! $usuario || $usuario->activado) {
                return false;
            }

            $usuario->contrasena_hash = Hash::make($contrasena);
            $usuario->sal = null;
            $usuario->activado = true;
            $usuario->save();

            return true;
        });
    }

    public function crearCuentaTecnico(array $datos): string
    {
        return DB::transaction(function () use ($datos): string {
            $codigo = Str::random(32);
            Usuario::query()->create([
                'nombre_usuario' => $datos['nombre_usuario'],
                'id_tecnico' => $datos['id_tecnico'],
                'rol' => 'tecnico',
                'codigo_activacion' => $codigo,
            ]);

            return $codigo;
        });
    }
}
