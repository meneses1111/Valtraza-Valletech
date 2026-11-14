<?php

namespace App\Http\Requests\ClientesEquipos;

use App\Http\Requests\SolicitudAdministrador;
use App\Rules\TextoValido;
use Illuminate\Validation\Rule;

class RegistrarClienteRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'nombre' => ['bail', 'required', 'string', new TextoValido, 'max:100'],
            'numero_identificacion' => ['bail', 'required', 'string', new TextoValido, 'max:20', Rule::unique('cliente', 'numero_identificacion')],
            'correo' => ['bail', 'nullable', 'string', new TextoValido, 'max:100', 'email:rfc'],
        ];
    }
}
