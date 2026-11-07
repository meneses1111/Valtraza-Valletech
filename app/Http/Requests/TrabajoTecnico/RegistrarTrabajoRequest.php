<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Enums\TipoTrabajo;
use Illuminate\Validation\Rule;

class RegistrarTrabajoRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::enum(TipoTrabajo::class)],
            'descripcion' => ['required', 'string'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
