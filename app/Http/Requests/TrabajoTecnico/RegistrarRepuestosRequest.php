<?php

namespace App\Http\Requests\TrabajoTecnico;

class RegistrarRepuestosRequest extends SolicitudTecnico
{
    public function rules(): array
    {
        return [
            'repuestos' => ['required', 'array', 'min:1'],
            'repuestos.*' => ['array:nombre,cantidad,observacion'],
            'repuestos.*.nombre' => ['required', 'string', 'max:100'],
            'repuestos.*.cantidad' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'repuestos.*.observacion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
