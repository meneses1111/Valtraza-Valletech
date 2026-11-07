<?php

namespace App\Http\Requests\TrabajoTecnico;

use App\Services\Servicios\GestionServicios;
use Illuminate\Foundation\Http\FormRequest;

class SolicitudTecnico extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()?->rol !== 'tecnico') {
            return false;
        }

        if ($this->route('servicio') !== null) {
            app(GestionServicios::class)->asignado((int) $this->route('servicio'), $this->user());
        }

        return true;
    }

    public function messages(): array
    {
        return [
            'required' => 'Completa este campo.',
            'string' => 'Escribe un texto válido.',
            'array' => 'Revisa los repuestos del formulario.',
            'boolean' => 'Selecciona una opción válida.',
            'integer' => 'Escribe una cantidad entera.',
            'min' => 'Registra al menos un repuesto y cantidades mayores que cero.',
            'max' => 'El valor supera la longitud o cantidad permitida.',
            'enum' => 'Selecciona una opción válida.',
        ];
    }
}
