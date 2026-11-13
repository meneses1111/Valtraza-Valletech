<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class RegistrarDiagnosticoRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'sin_fallas' => ['sometimes', 'boolean'],
            'resultado' => ['bail', Rule::requiredIf(! $this->boolean('sin_fallas')), 'nullable', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
            'pruebas_realizadas' => ['bail', 'nullable', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
            'observaciones' => ['bail', 'nullable', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
        ];
    }
}
