<?php

namespace App\Services\TrabajoTecnico;

use App\Enums\EstadoServicio;
use App\Models\Diagnostico;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\Usuario;
use App\Services\Servicios\GestionServicios;
use App\States\Servicios\EstadoDelServicio;
use App\Support\EntradaFormulario;

class RegistroTrabajoTecnico
{
    public function __construct(private GestionServicios $servicios) {}

    public function detalle(int $idServicio, Usuario $usuario): array
    {
        $servicio = $this->servicios->asignado($idServicio, $usuario);
        $diagnosticos = $servicio->diagnosticos()->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'diagnosticos_pagina')->withQueryString();
        $trabajos = $servicio->trabajos()->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'trabajos_pagina')->withQueryString();
        $repuestos = $servicio->repuestos()->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'repuestos_pagina')->withQueryString();
        $servicio->setRelation('diagnosticos', $diagnosticos->getCollection());
        $servicio->setRelation('trabajos', $trabajos->getCollection());
        $servicio->setRelation('repuestos', $repuestos->getCollection());
        $historial = $this->servicios->historialRelacionado($servicio, $usuario);
        $trabajosHistoricos = Trabajo::query()->whereIn('id_servicio', $historial->getCollection()->modelKeys())
            ->orderByDesc('id_trabajo')->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'historial_trabajos_pagina')->withQueryString();

        return compact('servicio', 'historial', 'diagnosticos', 'trabajos', 'repuestos', 'trabajosHistoricos');
    }

    public function registrarDiagnostico(int $idServicio, Usuario $usuario, array $datos): void
    {
        $this->servicios->conServicioAsignado($idServicio, $usuario, function (Servicio $servicio) use ($datos): void {
            $sinFallas = (bool) ($datos['sin_fallas'] ?? false);
            Diagnostico::query()->create([
                'id_servicio' => $servicio->id_servicio,
                'resultado' => $sinFallas ? 'No se encontraron fallas' : $datos['resultado'],
                'fallas_identificadas' => $sinFallas ? null : $datos['resultado'],
                'pruebas_realizadas' => $datos['pruebas_realizadas'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);
        });
    }

    public function registrarTrabajo(int $idServicio, Usuario $usuario, array $datos): void
    {
        $this->servicios->conServicioAsignado($idServicio, $usuario, function (Servicio $servicio) use ($datos): void {
            Trabajo::query()->create([...$datos, 'id_servicio' => $servicio->id_servicio]);
        });
    }

    public function registrarRepuestos(int $idServicio, Usuario $usuario, array $datos): void
    {
        $this->servicios->conServicioAsignado($idServicio, $usuario, function (Servicio $servicio) use ($datos): void {
            foreach ($datos['repuestos'] as $repuesto) {
                Repuesto::query()->create([...$repuesto, 'id_servicio' => $servicio->id_servicio]);
            }
        });
    }

    public function cambiarEstado(int $idServicio, Usuario $usuario, array $datos): void
    {
        $this->servicios->conServicioAsignado($idServicio, $usuario, function (Servicio $servicio) use ($datos): void {
            $tieneDiagnostico = Diagnostico::query()->where('id_servicio', $servicio->id_servicio)->exists();
            EstadoDelServicio::para(EstadoServicio::from($datos['estado']))->establecer($servicio, $tieneDiagnostico);
            $servicio->save();
        });
    }
}
