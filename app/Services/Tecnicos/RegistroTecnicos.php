<?php

namespace App\Services\Tecnicos;

use App\Models\Tecnico;
use App\Support\GuardarRegistro;
use Illuminate\Database\Eloquent\Collection;

class RegistroTecnicos
{
    public function listar(): Collection
    {
        return Tecnico::query()->orderBy('nombre')->get();
    }

    public function obtener(int $id): Tecnico
    {
        return Tecnico::query()->findOrFail($id);
    }

    public function registrar(array $datos): Tecnico
    {
        return GuardarRegistro::ejecutar(
            fn () => Tecnico::query()->create($datos),
            ['numero_identificacion' => 'Ya existe un técnico con esta identificación.']
        );
    }
}
