<?php

namespace App\States\Servicios;

use App\Enums\EstadoServicio;
use App\Models\Servicio;

abstract class EstadoDelServicio
{
    public static function para(EstadoServicio $estado): self
    {
        return match ($estado) {
            EstadoServicio::Pendiente => new Pendiente,
            EstadoServicio::EnCurso => new EnCurso,
            EstadoServicio::Completado => new Completado,
        };
    }

    abstract public function establecer(Servicio $servicio, bool $tieneDiagnostico): void;
}
