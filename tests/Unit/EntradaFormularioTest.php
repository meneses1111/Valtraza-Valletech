<?php

namespace Tests\Unit;

use App\Support\EntradaFormulario;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EntradaFormularioTest extends TestCase
{
    public function test_un_texto_se_conserva(): void
    {
        $this->assertSame('Equipo 1', EntradaFormulario::texto('Equipo 1'));
    }

    public function test_un_arreglo_manipulado_se_convierte_en_texto_vacio(): void
    {
        $this->assertSame('', EntradaFormulario::texto(['<script>']));
    }

    public function test_un_identificador_entero_positivo_es_aceptado(): void
    {
        $this->assertSame(15, EntradaFormulario::identificador('15'));
    }

    public static function identificadoresManipulados(): array
    {
        return [
            'cero' => ['0'],
            'negativo' => ['-3'],
            'decimal' => ['1.5'],
            'texto' => ['abc'],
            'inyeccion' => ['1 OR 1=1'],
            'arreglo' => [['1']],
            'nulo' => [null],
        ];
    }

    #[DataProvider('identificadoresManipulados')]
    public function test_un_identificador_manipulado_es_descartado(mixed $valor): void
    {
        $this->assertNull(EntradaFormulario::identificador($valor));
    }
}
