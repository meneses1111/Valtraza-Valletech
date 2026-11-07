<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use Illuminate\Validation\Rule;

class RegistrarServicioRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_cliente' => ['required', 'integer', Rule::exists('cliente', 'id_cliente')],
            'id_equipo' => ['required', 'integer', Rule::exists('equipo', 'id_equipo')->where('id_cliente', $this->integer('id_cliente'))],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion_problema' => ['required', 'string'],
        ];
    }
}
