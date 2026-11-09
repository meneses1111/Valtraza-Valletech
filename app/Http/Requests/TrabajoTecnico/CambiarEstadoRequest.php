<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Enums\EstadoServicio;
use Illuminate\Validation\Rule;

class CambiarEstadoRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::enum(EstadoServicio::class)],
        ];
    }
}
