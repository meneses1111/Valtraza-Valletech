<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class AsignarTecnicoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_tecnico' => ['bail', 'required', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('tecnico', 'id_tecnico')],
        ];
    }
}
