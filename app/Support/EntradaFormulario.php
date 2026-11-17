<?php

namespace App\Support;

class EntradaFormulario
{
    public const MAX_IDENTIFICADOR = 2147483647;

    public const MAX_TEXTO = 10000;

    public const MAX_REPUESTOS = 100;

    public const POR_PAGINA = 15;

    public const MAX_PAGINA = 10000;

    public static function texto(mixed $valor): string
    {
        return is_scalar($valor) ? (string) $valor : '';
    }

    public static function identificador(mixed $valor): ?int
    {
        if (! is_string($valor) && ! is_int($valor)) {
            return null;
        }

        $identificador = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => self::MAX_IDENTIFICADOR]]);

        return $identificador === false ? null : $identificador;
    }
}
