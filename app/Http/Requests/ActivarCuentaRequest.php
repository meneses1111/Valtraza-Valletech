<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivarCuentaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'codigo_activacion' => ['required', 'string', 'max:64'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
