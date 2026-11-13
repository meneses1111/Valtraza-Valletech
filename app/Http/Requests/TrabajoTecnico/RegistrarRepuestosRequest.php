<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Validation\ValidationException;

class RegistrarRepuestosRequest extends SolicitudTecnico
{
    public function validationData(): array
    {
        $datos = parent::validationData();

        if (is_array($datos['repuestos'] ?? null) && count($datos['repuestos']) > EntradaFormulario::MAX_REPUESTOS) {
            $this->merge(['repuestos' => array_slice($datos['repuestos'], 0, EntradaFormulario::MAX_REPUESTOS)]);

            throw ValidationException::withMessages(['repuestos' => 'Registra como máximo 100 repuestos por envío.']);
        }

        return $datos;
    }

    public function rules(): array
    {
        return [
            'repuestos' => ['bail', 'required', 'array', 'min:1', 'max:'.EntradaFormulario::MAX_REPUESTOS],
            'repuestos.*' => ['array:nombre,cantidad,observacion'],
            'repuestos.*.nombre' => ['bail', 'required', 'string', new TextoValido, 'max:100'],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'repuestos.*.observacion' => ['bail', 'nullable', 'string', new TextoValido, 'max:255'],
        ];
    }
}
