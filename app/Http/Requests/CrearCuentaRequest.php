<?php

namespace App\Http\Requests;

use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class CrearCuentaRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'nombre_usuario' => ['bail', 'required', 'string', new TextoValido, 'max:50', Rule::unique('usuario', 'nombre_usuario')],
            'id_tecnico' => ['bail', 'required', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('tecnico', 'id_tecnico'), Rule::unique('usuario', 'id_tecnico')],
        ];
    }
}
