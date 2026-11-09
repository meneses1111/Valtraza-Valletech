<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudAdministrador extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            'required' => 'Completa este campo.',
            'string' => 'Escribe un texto válido.',
            'max' => 'El valor supera la longitud o cantidad permitida.',
            'email' => 'Escribe un correo electrónico válido.',
            'unique' => 'La identificación ya está registrada.',
            'exists' => 'Selecciona un registro válido.',
            'date_format' => 'Selecciona una fecha válida.',
            'integer' => 'Escribe un número entero válido.',
            'in' => 'Selecciona una opción válida.',
        ];
    }
}
