<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class RegistrarServicioRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_cliente' => ['bail', 'required', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('cliente', 'id_cliente')],
            'id_equipo' => ['bail', 'required', 'integer', 'min:1', 'max:'.EntradaFormulario::MAX_IDENTIFICADOR, Rule::exists('equipo', 'id_equipo')->where('id_cliente', EntradaFormulario::identificador($this->input('id_cliente')) ?? 0)],
            'fecha' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:0001-01-01', 'before_or_equal:9999-12-31'],
            'nombre' => ['bail', 'required', 'string', new TextoValido, 'max:100'],
            'descripcion_problema' => ['bail', 'required', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
        ];
    }
}
