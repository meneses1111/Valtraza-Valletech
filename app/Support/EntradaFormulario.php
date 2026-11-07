<?php

namespace App\Support;

class EntradaFormulario
{
    public static function texto(mixed $valor): string
    {
        return is_scalar($valor) ? (string) $valor : '';
    }

    public static function identificador(mixed $valor): ?int
    {
        if (! is_string($valor) && ! is_int($valor)) {
            return null;
        }

        $identificador = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $identificador === false ? null : $identificador;
    }
}
