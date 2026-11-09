<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CodigosRegistro
{
    public function equipo(): string
    {
        DB::select('SELECT pg_advisory_xact_lock(70101)');
        $fila = DB::selectOne("SELECT COALESCE(MAX(substring(codigo_equipo from '[0-9]+$')::bigint), 0) + 1 AS siguiente FROM equipo");

        return 'EQ-'.str_pad((string) $fila->siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function servicio(): string
    {
        DB::select('SELECT pg_advisory_xact_lock(70102)');
        $fila = DB::selectOne("SELECT COALESCE(MAX(substring(codigo_servicio from '[0-9]+$')::bigint), 0) + 1 AS siguiente FROM servicio");

        return 'SRV-'.str_pad((string) $fila->siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function seguimiento(): string
    {
        return bin2hex(random_bytes(16));
    }
}
