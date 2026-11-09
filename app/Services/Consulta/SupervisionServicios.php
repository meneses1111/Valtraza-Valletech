<?php

namespace App\Services\Consulta;

use App\Models\Usuario;
use App\Services\Servicios\GestionServicios;
use App\Services\TrabajoTecnico\LecturaAvance;

class SupervisionServicios
{
    public function __construct(private GestionServicios $servicios, private LecturaAvance $avance) {}

    public function listar(Usuario $usuario, array $filtros): mixed
    {
        abort_unless($usuario->rol === 'administrador', 403);

        return $this->servicios->listar(
            $filtros['asignacion'] ?? null,
            $filtros['estado'] ?? null,
            isset($filtros['id_tecnico']) ? (int) $filtros['id_tecnico'] : null
        );
    }

    public function detalle(Usuario $usuario, int $idServicio): array
    {
        abort_unless($usuario->rol === 'administrador', 403);

        return [
            'servicio' => $this->servicios->obtener($idServicio),
            'avance' => $this->avance->paraAdministrador($usuario, $idServicio),
        ];
    }
}
