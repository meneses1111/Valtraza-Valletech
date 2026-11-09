<?php

namespace App\Enums;

enum TipoTrabajo: string
{
    case Mantenimiento = 'mantenimiento';
    case Reparacion = 'reparacion';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Mantenimiento => 'Mantenimiento',
            self::Reparacion => 'Reparación',
        };
    }
}
