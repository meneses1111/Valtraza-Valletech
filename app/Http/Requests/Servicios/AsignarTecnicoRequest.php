<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use Illuminate\Validation\Rule;

class AsignarTecnicoRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_tecnico' => ['required', 'integer', Rule::exists('tecnico', 'id_tecnico')],
        ];
    }
}
