<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearCuentaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre_usuario' => ['required', 'string', 'max:50', Rule::unique('usuario', 'nombre_usuario')],
            'id_tecnico' => ['required', 'integer', Rule::exists('tecnico', 'id_tecnico'), Rule::unique('usuario', 'id_tecnico')],
        ];
    }
}
