<?php

namespace App\Services\Servicios;

use App\Models\Servicio;
use App\Support\EntradaFormulario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HistorialEquipos
{
    public function consultar(Collection $equipos): LengthAwarePaginator
    {
        return Servicio::query()->whereIn('id_equipo', $equipos->pluck('id_equipo'))
            ->orderByDesc('fecha')->orderByDesc('id_servicio')
            ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'historial_pagina')->withQueryString();
    }
}
