<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use Illuminate\Validation\Rule;

class FiltrarServiciosRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'asignacion' => ['nullable', Rule::in(['sin_tecnico', 'con_tecnico'])],
        ];
    }
}
