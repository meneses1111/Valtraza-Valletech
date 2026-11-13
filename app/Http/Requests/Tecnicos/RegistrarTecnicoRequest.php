<?php

namespace App\Http\Requests\Tecnicos;

use App\Http\Requests\SolicitudAdministrador;
use App\Rules\TextoValido;
use Illuminate\Validation\Rule;

class RegistrarTecnicoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'nombre' => ['bail', 'required', 'string', new TextoValido, 'max:100'],
            'numero_identificacion' => ['bail', 'required', 'string', new TextoValido, 'max:20', Rule::unique('tecnico', 'numero_identificacion')],
            'correo' => ['bail', 'nullable', 'string', new TextoValido, 'max:100', 'email:rfc'],
        ];
    }
}
