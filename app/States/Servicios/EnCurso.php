<?php

namespace App\States\Servicios;

use App\Models\Servicio;

class EnCurso extends EstadoDelServicio
{
    public function establecer(Servicio $servicio, bool $tieneDiagnostico): void
    {
        $servicio->estado = 'en_curso';
    }
}
