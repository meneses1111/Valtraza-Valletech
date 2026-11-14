<?php

namespace App\Services\Servicios;

use App\Models\Servicio;
use App\Models\Usuario;
use App\Services\ClientesEquipos\RegistroClientesEquipos;
use App\Services\Tecnicos\RegistroTecnicos;
use App\Support\EntradaFormulario;
use App\Support\GuardarRegistro;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GestionServicios
{
    public function __construct(
        private RegistroClientesEquipos $clientes,
        private RegistroTecnicos $tecnicos
    ) {}

    public function listar(?string $asignacion, ?string $estado = null, ?int $idTecnico = null): mixed
    {
        return Servicio::query()->with(['equipo.cliente', 'tecnico'])
            ->when($asignacion === 'sin_tecnico', fn ($consulta) => $consulta->whereNull('id_tecnico'))
            ->when($asignacion === 'con_tecnico', fn ($consulta) => $consulta->whereNotNull('id_tecnico'))
            ->when($estado, fn ($consulta) => $consulta->where('estado', $estado))
            ->when($idTecnico, fn ($consulta) => $consulta->where('id_tecnico', $idTecnico))
            ->orderByDesc('fecha')->orderByDesc('id_servicio')->paginate(EntradaFormulario::POR_PAGINA)->withQueryString();
    }

    public function obtener(int $id): Servicio
    {
        return Servicio::query()->with(['equipo.cliente', 'tecnico'])->findOrFail($id);
    }

    public function paraSeguimiento(string $codigo): ?array
    {
        $servicio = Servicio::query()->select(['id_servicio', 'nombre', 'estado', 'id_equipo'])
            ->with('equipo:id_equipo,codigo_equipo,modelo')
            ->where('codigo_seguimiento', $codigo)->first();

        if (! $servicio) {
            return null;
        }

        return [
            'id_servicio' => $servicio->id_servicio,
            'nombre' => $servicio->nombre,
            'estado' => $servicio->estado,
            'equipo_codigo' => $servicio->equipo->codigo_equipo,
            'equipo_modelo' => $servicio->equipo->modelo,
        ];
    }

    public function registrar(array $datos): Servicio
    {
        $equipo = $this->clientes->equipoDelCliente((int) $datos['id_equipo'], (int) $datos['id_cliente']);

        for ($intento = 0; $intento < 3; $intento++) {
            try {
                return DB::transaction(fn () => Servicio::query()->create([
                    'id_equipo' => $equipo->id_equipo,
                    'fecha' => $datos['fecha'],
                    'nombre' => $datos['nombre'],
                    'descripcion_problema' => $datos['descripcion_problema'],
                    'estado' => 'pendiente',
                ]), 3);
            } catch (QueryException $exception) {
                $estado = $exception->errorInfo[0] ?? null;

                if ($estado === '23505') {
                    continue;
                }

                if (in_array($estado, ['23503', '23514', '23502'], true)) {
                    throw ValidationException::withMessages(['registro' => 'Revisa el cliente, el computador y los datos del servicio.']);
                }

                throw $exception;
            }
        }

        throw ValidationException::withMessages(['registro' => 'No se pudo generar un código único. Intenta guardar nuevamente.']);
    }

    public function asignar(int $idServicio, int $idTecnico): Servicio
    {
        $tecnico = $this->tecnicos->obtener($idTecnico);

        return GuardarRegistro::ejecutar(function () use ($idServicio, $tecnico): Servicio {
            $servicio = Servicio::query()->lockForUpdate()->findOrFail($idServicio);
            $servicio->id_tecnico = $tecnico->id_tecnico;
            $servicio->save();

            return $servicio;
        });
    }

    public function asignados(Usuario $usuario, ?string $estado): mixed
    {
        abort_unless($usuario->rol === 'tecnico' && $usuario->id_tecnico !== null, 403);

        return Servicio::query()->with('equipo')
            ->where('id_tecnico', $usuario->id_tecnico)
            ->when($estado, fn ($consulta) => $consulta->where('estado', $estado))
            ->orderByDesc('fecha')->orderByDesc('id_servicio')->paginate(EntradaFormulario::POR_PAGINA)->withQueryString();
    }

    public function asignado(int $idServicio, Usuario $usuario): Servicio
    {
        $servicio = $this->obtener($idServicio);
        $this->comprobarEncargado($servicio, $usuario);

        return $servicio;
    }

    public function conServicioAsignado(int $idServicio, Usuario $usuario, Closure $operacion): mixed
    {
        return GuardarRegistro::ejecutar(function () use ($idServicio, $usuario, $operacion) {
            $servicio = Servicio::query()->lockForUpdate()->findOrFail($idServicio);
            $actual = Usuario::query()->lockForUpdate()->find($usuario->getAuthIdentifier());
            abort_unless($actual?->activado, 403);
            $this->comprobarEncargado($servicio, $actual);

            return $operacion($servicio);
        });
    }

    public function historialRelacionado(Servicio $servicio, Usuario $usuario): mixed
    {
        $this->comprobarEncargado($servicio, $usuario);

        return Servicio::query()->withCount('trabajos')
            ->where('id_tecnico', $usuario->id_tecnico)
            ->where('id_equipo', $servicio->id_equipo)->where('estado', 'completado')
            ->where('id_servicio', '<>', $servicio->id_servicio)
            ->orderByDesc('fecha')->orderByDesc('id_servicio')
            ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'historial_pagina')->withQueryString();
    }

    private function comprobarEncargado(Servicio $servicio, Usuario $usuario): void
    {
        abort_unless(
            $usuario->rol === 'tecnico'
            && $usuario->id_tecnico !== null
            && (int) $servicio->id_tecnico === (int) $usuario->id_tecnico,
            403
        );
    }
}
