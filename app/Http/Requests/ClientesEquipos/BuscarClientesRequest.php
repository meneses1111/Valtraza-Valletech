<?php

namespace App\Http\Requests\ClientesEquipos;

use App\Http\Requests\SolicitudAdministrador;

class BuscarClientesRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'numero_identificacion' => ['nullable', 'string', 'max:20'],
        ];
    }
}
