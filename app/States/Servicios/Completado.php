<?php

namespace App\States\Servicios;

use App\Models\Servicio;
use Illuminate\Validation\ValidationException;

class Completado extends EstadoDelServicio
{
    public function establecer(Servicio $servicio, bool $tieneDiagnostico): void
    {
        if (! $tieneDiagnostico) {
            throw ValidationException::withMessages(['estado' => 'No puedes completar el servicio sin registrar al menos un diagnóstico.']);
        }

        $servicio->estado = 'completado';
    }
}
