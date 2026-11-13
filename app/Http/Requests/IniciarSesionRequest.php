<?php

namespace App\Http\Requests;

use App\Rules\TextoValido;
use App\Support\AuditoriaSeguridad;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class IniciarSesionRequest extends FormRequest
{
    public function rules(): array
    {
        return ['nombre_usuario' => ['bail', 'required', 'string', new TextoValido, 'max:50'], 'password' => ['required', 'string', 'max:72']];
    }

    protected function failedValidation(Validator $validator)
    {
        AuditoriaSeguridad::registrar('login_invalido', $this);
        parent::failedValidation($validator);
    }
}
