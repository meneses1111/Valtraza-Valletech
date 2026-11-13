<?php

namespace App\Http\Requests\Servicios;

use App\Enums\EstadoServicio;
use App\Http\Requests\SolicitudAdministrador;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class FiltrarServiciosRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'asignacion' => ['nullable', Rule::in(['sin_tecnico', 'con_tecnico'])],
            'estado' => ['nullable', Rule::enum(EstadoServicio::class)],
            'id_tecnico' => ['bail', 'nullable', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('tecnico', 'id_tecnico')],
        ];
    }
}
