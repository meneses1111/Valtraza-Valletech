<?php

namespace App\Http\Requests\ClientesEquipos;

use App\Http\Requests\SolicitudAdministrador;
use App\Rules\TextoValido;
use App\Support\EntradaFormulario;

class RegistrarEquipoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'fecha_registro' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:0001-01-01', 'before_or_equal:9999-12-31'],
            'modelo' => ['bail', 'nullable', 'string', new TextoValido, 'max:100'],
            'otros_datos_tecnicos' => ['bail', 'nullable', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
        ];
    }
}
