<?php

namespace App\Http\Requests\ClientesEquipos;

use App\Http\Requests\SolicitudAdministrador;

class RegistrarEquipoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'fecha_registro' => ['required', 'date_format:Y-m-d'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'otros_datos_tecnicos' => ['nullable', 'string'],
        ];
    }
}
