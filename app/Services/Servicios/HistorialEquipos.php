<?php

namespace App\Services\Servicios;

use App\Models\Servicio;
use Illuminate\Support\Collection;

class HistorialEquipos
{
    public function consultar(Collection $equipos): Collection
    {
        return Servicio::query()->whereIn('id_equipo', $equipos->pluck('id_equipo'))
            ->orderByDesc('fecha')->orderByDesc('id_servicio')->get()->groupBy('id_equipo');
    }
}
