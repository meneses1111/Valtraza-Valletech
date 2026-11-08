<?php

namespace App\Services\TrabajoTecnico;

use App\Models\Diagnostico;
use App\Models\Repuesto;
use App\Models\Trabajo;
use App\Models\Usuario;

class LecturaAvance
{
    public function paraAdministrador(Usuario $usuario, int $idServicio): array
    {
        abort_unless($usuario->rol === 'administrador', 403);

        return [
            'diagnosticos' => Diagnostico::query()->where('id_servicio', $idServicio)->orderBy('id_diagnostico')->get(),
            'trabajos' => Trabajo::query()->where('id_servicio', $idServicio)->orderBy('id_trabajo')->get(),
            'repuestos' => Repuesto::query()->where('id_servicio', $idServicio)->orderBy('id_repuesto')->get(),
        ];
    }
}
