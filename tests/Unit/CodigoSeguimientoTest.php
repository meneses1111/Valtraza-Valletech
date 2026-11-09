<?php

namespace Tests\Unit;

use App\Support\CodigosRegistro;
use PHPUnit\Framework\TestCase;

class CodigoSeguimientoTest extends TestCase
{
    public function test_el_codigo_de_seguimiento_tiene_32_caracteres_hexadecimales(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', (new CodigosRegistro)->seguimiento());
    }

    public function test_los_codigos_de_seguimiento_no_se_repiten(): void
    {
        $codigos = new CodigosRegistro;
        $generados = array_map(fn () => $codigos->seguimiento(), range(1, 2000));

        $this->assertCount(2000, array_unique($generados));
    }
}
