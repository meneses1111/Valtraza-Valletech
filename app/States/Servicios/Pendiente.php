<?php

namespace App\States\Servicios;

use App\Models\Servicio;

class Pendiente extends EstadoDelServicio
{
    public function establecer(Servicio $servicio, bool $tieneDiagnostico): void
    {
        $servicio->estado = 'pendiente';
    }
}
