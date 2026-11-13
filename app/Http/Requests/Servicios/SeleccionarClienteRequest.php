<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class SeleccionarClienteRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_cliente' => ['bail', 'nullable', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('cliente', 'id_cliente')],
            'id_equipo' => ['bail', 'nullable', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('equipo', 'id_equipo')->where('id_cliente', EntradaFormulario::identificador($this->input('id_cliente')) ?? 0)],
        ];
    }
}
