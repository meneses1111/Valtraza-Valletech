<?php

namespace App\Observers;

use App\Models\Equipo;
use App\Support\CodigosRegistro;

class EquipoObserver
{
    public function creating(Equipo $equipo): void
    {
        if (! $equipo->codigo_equipo) {
            $equipo->codigo_equipo = app(CodigosRegistro::class)->equipo();
        }
    }
}
