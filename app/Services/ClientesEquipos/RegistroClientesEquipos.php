<?php

namespace App\Services\ClientesEquipos;

use App\Models\Cliente;
use App\Models\Equipo;
use App\Support\GuardarRegistro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class RegistroClientesEquipos
{
    public function buscar(?string $identificacion): mixed
    {
        return Cliente::query()
            ->when($identificacion, fn ($consulta) => $consulta->where('numero_identificacion', $identificacion))
            ->orderBy('nombre')->paginate(20)->withQueryString();
    }

    public function listar(): Collection
    {
        return Cliente::query()->orderBy('nombre')->get();
    }

    public function obtener(int $id): Cliente
    {
        return Cliente::query()->with('equipos')->findOrFail($id);
    }

    public function buscarPorIdentificacion(?string $identificacion): ?Cliente
    {
        return $identificacion ? Cliente::query()->where('numero_identificacion', $identificacion)->first() : null;
    }

    public function equipoDelCliente(int $idEquipo, int $idCliente): Equipo
    {
        $equipo = Equipo::query()->where('id_cliente', $idCliente)->find($idEquipo);

        if (! $equipo) {
            throw ValidationException::withMessages(['id_equipo' => 'Selecciona un computador del cliente indicado.']);
        }

        return $equipo;
    }

    public function registrarCliente(array $datos): Cliente
    {
        return GuardarRegistro::ejecutar(
            fn () => Cliente::query()->create($datos),
            ['numero_identificacion' => 'Este cliente ya existe. Puedes consultar su ficha.']
        );
    }

    public function registrarEquipo(int $idCliente, array $datos): Equipo
    {
        $this->obtener($idCliente);

        return GuardarRegistro::ejecutar(
            fn () => Equipo::query()->create([...$datos, 'id_cliente' => $idCliente])
        );
    }
}
