<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IniciarSesionRequest extends FormRequest
{
    public function rules(): array
    {
        return ['nombre_usuario' => ['required', 'string', 'max:50'], 'password' => ['required', 'string']];
    }
}
