<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EsquemaTest extends TestCase
{
    use RefreshDatabase;

    public static function tipos(): array
    {
        return [
            ['rol_usuario', ['administrador', 'tecnico']],
            ['estado_servicio', ['pendiente', 'en_curso', 'completado']],
            ['tipo_trabajo', ['mantenimiento', 'reparacion']],
        ];
    }

    #[DataProvider('tipos')]
    public function test_los_enum_conservan_los_valores_del_diseno(string $tipo, array $valores): void
    {
        $filas = DB::select('SELECT unnest(enum_range(NULL::'.$tipo.'))::text AS valor');

        $this->assertSame($valores, array_column($filas, 'valor'));
    }

    #[DataProvider('tipos')]
    public function test_los_enum_rechazan_valores_desconocidos(string $tipo, array $valores): void
    {
        try {
            DB::select('SELECT CAST(? AS '.$tipo.')', ['valor_invalido']);
            $this->fail('PostgreSQL aceptó un valor ajeno al ENUM.');
        } catch (QueryException $exception) {
            $this->assertSame('22P02', $exception->errorInfo[0]);
        }
    }
}
