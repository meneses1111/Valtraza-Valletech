<?php

namespace App\Support;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuardarRegistro
{
    public static function ejecutar(Closure $operacion, array $erroresUnicos = []): mixed
    {
        try {
            return DB::transaction($operacion, 3);
        } catch (QueryException $exception) {
            $estado = $exception->errorInfo[0] ?? null;

            if ($estado === '23505') {
                throw ValidationException::withMessages($erroresUnicos ?: ['registro' => 'El registro ya existe. Revisa los datos.']);
            }

            if (in_array($estado, ['23503', '23514', '23502', '22003'], true)) {
                throw ValidationException::withMessages(['registro' => 'No se pudo guardar. Revisa los datos y los registros seleccionados.']);
            }

            throw $exception;
        }
    }
}
