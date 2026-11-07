<?php

namespace App\Http\Requests\Tecnicos;

use App\Http\Requests\SolicitudAdministrador;
use Illuminate\Validation\Rule;

class RegistrarTecnicoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'numero_identificacion' => ['required', 'string', 'max:20', Rule::unique('tecnico', 'numero_identificacion')],
            'correo' => ['nullable', 'email:rfc', 'max:100'],
        ];
    }
}
