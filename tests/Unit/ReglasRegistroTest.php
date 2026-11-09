<?php

namespace Tests\Unit;

use App\Http\Requests\ClientesEquipos\RegistrarEquipoRequest;
use App\Http\Requests\Consulta\ConsultarServicioRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReglasRegistroTest extends TestCase
{
    private function falla(string $solicitud, array $datos): bool
    {
        return Validator::make($datos, (new $solicitud)->rules())->fails();
    }

    public static function fechasInvalidas(): array
    {
        return [
            'dia inexistente' => ['2026-02-31'],
            'formato dia mes anio' => ['31/12/2026'],
            'mes trece' => ['2026-13-01'],
            'texto' => ['ayer'],
            'vacia' => [''],
        ];
    }

    #[DataProvider('fechasInvalidas')]
    public function test_el_equipo_rechaza_una_fecha_invalida(string $fecha): void
    {
        $this->assertTrue($this->falla(RegistrarEquipoRequest::class, ['fecha_registro' => $fecha]));
    }

    public function test_el_equipo_acepta_una_fecha_valida_sin_datos_opcionales(): void
    {
        $this->assertFalse($this->falla(RegistrarEquipoRequest::class, ['fecha_registro' => '2026-02-28']));
    }

    public function test_el_equipo_acepta_el_29_de_febrero_de_un_anio_bisiesto(): void
    {
        $this->assertFalse($this->falla(RegistrarEquipoRequest::class, ['fecha_registro' => '2028-02-29']));
    }

    public function test_la_consulta_exige_el_codigo(): void
    {
        $this->assertTrue($this->falla(ConsultarServicioRequest::class, ['codigo_seguimiento' => '']));
    }

    public function test_la_consulta_acepta_un_codigo_de_32_caracteres(): void
    {
        $this->assertFalse($this->falla(ConsultarServicioRequest::class, ['codigo_seguimiento' => str_repeat('a', 32)]));
    }

    public function test_la_consulta_rechaza_un_codigo_de_33_caracteres(): void
    {
        $this->assertTrue($this->falla(ConsultarServicioRequest::class, ['codigo_seguimiento' => str_repeat('a', 33)]));
    }

    public function test_la_consulta_rechaza_un_codigo_enviado_como_arreglo(): void
    {
        $this->assertTrue($this->falla(ConsultarServicioRequest::class, ['codigo_seguimiento' => ['a']]));
    }
}
