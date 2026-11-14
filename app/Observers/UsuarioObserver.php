<?php

namespace App\Observers;

use App\Models\Usuario;
use App\Support\AuditoriaSeguridad;
use Illuminate\Support\Facades\DB;

class UsuarioObserver
{
    public function updated(Usuario $usuario): void
    {
        if (! $usuario->wasChanged(['rol', 'activado'])) {
            return;
        }

        $datos = [
            'cuenta_id' => $usuario->getKey(),
            'rol_anterior' => $usuario->getOriginal('rol'),
            'rol_actual' => $usuario->rol,
            'activado' => $usuario->activado,
        ];
        $cambioRol = $usuario->wasChanged('rol');
        DB::afterCommit(fn () => AuditoriaSeguridad::registrar('cuenta_actualizada', request(), $datos, $cambioRol));
    }
}
