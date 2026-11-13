<?php

namespace App\Services\Tecnicos;

use App\Models\Tecnico;
use App\Support\EntradaFormulario;
use App\Support\GuardarRegistro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RegistroTecnicos
{
    public function listar(): LengthAwarePaginator
    {
        return Tecnico::query()->orderBy('nombre')->orderBy('id_tecnico')
            ->paginate(EntradaFormulario::POR_PAGINA, ['*'], 'tecnicos_pagina')->withQueryString();
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
