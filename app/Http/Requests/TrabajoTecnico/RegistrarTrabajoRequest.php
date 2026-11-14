<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Enums\TipoTrabajo;
use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Validation\Rule;

class RegistrarTrabajoRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::enum(TipoTrabajo::class)],
            'descripcion' => ['bail', 'required', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
            'observaciones' => ['bail', 'nullable', 'string', new TextoValido, 'max:'.EntradaFormulario::MAX_TEXTO],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'Falta indicar si es un mantenimiento o una reparación.',
            'descripcion.required' => 'Describe el trabajo realizado.',
        ] + parent::messages();
    }
}
