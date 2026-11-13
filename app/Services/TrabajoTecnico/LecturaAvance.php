<?php

namespace App\Services\TrabajoTecnico;

use App\Models\Diagnostico;
use App\Models\Repuesto;
use App\Models\Trabajo;
use App\Models\Usuario;
use App\Support\EntradaFormulario;

class LecturaAvance
{
    public function paraAdministrador(Usuario $usuario, int $idServicio): array
    {
        abort_unless($usuario->rol === 'administrador', 403);

        return [
            'diagnosticos' => Diagnostico::query()->where('id_servicio', $idServicio)->orderBy('id_diagnostico')
                ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'diagnosticos_pagina')->withQueryString(),
            'trabajos' => Trabajo::query()->where('id_servicio', $idServicio)->orderBy('id_trabajo')
                ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'trabajos_pagina')->withQueryString(),
            'repuestos' => Repuesto::query()->where('id_servicio', $idServicio)->orderBy('id_repuesto')
                ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'repuestos_pagina')->withQueryString(),
        ];
    }

    public function resumenPublico(int $idServicio): array
    {
        $tipos = Trabajo::query()->where('id_servicio', $idServicio)->distinct()->pluck('tipo');

        return [
            'diagnosticos' => Diagnostico::query()->where('id_servicio', $idServicio)->exists(),
            'mantenimientos' => $tipos->contains('mantenimiento'),
            'reparaciones' => $tipos->contains('reparacion'),
        ];
    }
}
