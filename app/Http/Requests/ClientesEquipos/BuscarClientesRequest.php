<?php

namespace App\Http\Requests\ClientesEquipos;

use App\Http\Requests\SolicitudAdministrador;
use App\Rules\TextoValido;

class BuscarClientesRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'numero_identificacion' => ['bail', 'nullable', 'string', new TextoValido, 'max:20'],
        ];
    }
}
