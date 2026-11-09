<?php

return [
    'required' => 'Completa el campo :attribute.',
    'string' => 'El campo :attribute debe contener texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'boolean' => 'Selecciona una opción válida en :attribute.',
    'array' => 'Revisa la información de :attribute.',
    'email' => 'Escribe un correo electrónico válido.',
    'confirmed' => 'La confirmación de la contraseña no coincide.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'exists' => 'Selecciona un registro válido para :attribute.',
    'enum' => 'Selecciona una opción válida para :attribute.',
    'in' => 'Selecciona una opción válida para :attribute.',
    'date' => 'Selecciona una fecha válida.',
    'date_format' => 'Selecciona una fecha válida para :attribute.',
    'max' => [
        'array' => 'El campo :attribute contiene demasiados elementos.',
        'numeric' => 'El valor de :attribute no puede superar :max.',
        'string' => 'El campo :attribute no puede superar :max caracteres.',
    ],
    'min' => [
        'array' => 'Registra al menos :min elemento.',
        'numeric' => 'El valor de :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'attributes' => [
        'nombre_usuario' => 'usuario',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'codigo_activacion' => 'código de activación',
        'codigo_seguimiento' => 'código de seguimiento',
        'id_tecnico' => 'técnico',
        'id_cliente' => 'cliente',
        'id_equipo' => 'computador',
        'numero_identificacion' => 'número de identificación',
    ],
];
