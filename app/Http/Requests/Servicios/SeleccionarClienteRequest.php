<?php

namespace App\Http\Requests\Servicios;

use App\Http\Requests\SolicitudAdministrador;
use Illuminate\Validation\Rule;

class SeleccionarClienteRequest extends SolicitudAdministrador
{
    public function rules(): array
    {
        return [
            'id_cliente' => ['nullable', 'integer', Rule::exists('cliente', 'id_cliente')],
        ];
    }
}
