<?php

namespace App\Http\Requests\TrabajoTecnico;

use Illuminate\Validation\Rule;

class RegistrarDiagnosticoRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'sin_fallas' => ['sometimes', 'boolean'],
            'resultado' => [Rule::requiredIf(! $this->boolean('sin_fallas')), 'nullable', 'string'],
            'pruebas_realizadas' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
