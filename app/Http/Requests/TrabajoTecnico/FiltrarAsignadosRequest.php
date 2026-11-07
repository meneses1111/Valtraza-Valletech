<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Enums\EstadoServicio;
use Illuminate\Validation\Rule;

class FiltrarAsignadosRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'estado' => ['nullable', Rule::enum(EstadoServicio::class)],
        ];
    }
}
