<?php

namespace App\Events;

class ServicioActualizado
{
    public function __construct(public readonly int $idServicio) {}
}
