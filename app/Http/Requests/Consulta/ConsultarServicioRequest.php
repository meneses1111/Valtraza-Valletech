<?php

namespace App\Http\Requests\Consulta;

use App\Rules\TextoValido;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['codigo_seguimiento' => ['bail', 'required', 'string', new TextoValido, 'max:32']];
    }

    public function messages(): array
    {
        return [
            'codigo_seguimiento.required' => 'Escribe el código de seguimiento.',
            'codigo_seguimiento.string' => 'Escribe un código de seguimiento válido.',
            'codigo_seguimiento.max' => 'El código no puede superar los 32 caracteres.',
        ];
    }
}
