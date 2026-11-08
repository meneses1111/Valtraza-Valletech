<?php

namespace App\Services\Consulta;

use App\Services\Servicios\GestionServicios;
use App\Services\TrabajoTecnico\LecturaAvance;

class ConsultaPublica
{
    public function __construct(private GestionServicios $servicios, private LecturaAvance $avance) {}

    public function consultar(string $codigo): ?array
    {
        $servicio = $this->servicios->paraSeguimiento($codigo);

        if (! $servicio) {
            return null;
        }

        $resumen = $this->avance->resumenPublico($servicio['id_servicio']);

        return [
            'nombre' => $servicio['nombre'],
            'estado' => $servicio['estado'],
            'equipo_codigo' => $servicio['equipo_codigo'],
            'equipo_modelo' => $servicio['equipo_modelo'],
            'avance' => [
                'diagnosticos' => (bool) ($resumen['diagnosticos'] ?? false),
                'mantenimientos' => (bool) ($resumen['mantenimientos'] ?? false),
                'reparaciones' => (bool) ($resumen['reparaciones'] ?? false),
            ],
        ];
    }
}
