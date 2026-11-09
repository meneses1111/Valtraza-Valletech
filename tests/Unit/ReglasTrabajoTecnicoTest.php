<?php

namespace Tests\Unit;

use App\Http\Requests\TrabajoTecnico\CambiarEstadoRequest;
use App\Http\Requests\TrabajoTecnico\RegistrarDiagnosticoRequest;
use App\Http\Requests\TrabajoTecnico\RegistrarRepuestosRequest;
use App\Http\Requests\TrabajoTecnico\RegistrarTrabajoRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReglasTrabajoTecnicoTest extends TestCase
{
    private function falla(string $solicitud, array $datos): bool
    {
        $reglas = $solicitud::create('/', 'POST', $datos)->rules();

        return Validator::make($datos, $reglas)->fails();
    }

    public function test_el_diagnostico_exige_un_resultado(): void
    {
        $this->assertTrue($this->falla(RegistrarDiagnosticoRequest::class, ['resultado' => '']));
    }

    public function test_el_diagnostico_sin_fallas_no_exige_resultado(): void
    {
        $this->assertFalse($this->falla(RegistrarDiagnosticoRequest::class, ['sin_fallas' => '1']));
    }

    public function test_el_diagnostico_con_resultado_es_aceptado(): void
    {
        $this->assertFalse($this->falla(RegistrarDiagnosticoRequest::class, ['resultado' => 'Fuente dañada']));
    }

    public function test_la_actividad_exige_descripcion(): void
    {
        $this->assertTrue($this->falla(RegistrarTrabajoRequest::class, ['tipo' => 'mantenimiento', 'descripcion' => '']));
    }

    public function test_la_actividad_exige_un_tipo(): void
    {
        $this->assertTrue($this->falla(RegistrarTrabajoRequest::class, ['descripcion' => 'Limpieza']));
    }

    public function test_la_actividad_rechaza_un_tipo_ajeno(): void
    {
        $this->assertTrue($this->falla(RegistrarTrabajoRequest::class, ['tipo' => 'instalacion', 'descripcion' => 'Limpieza']));
    }

    public static function tiposValidos(): array
    {
        return [['mantenimiento'], ['reparacion']];
    }

    #[DataProvider('tiposValidos')]
    public function test_la_actividad_acepta_mantenimiento_y_reparacion(string $tipo): void
    {
        $this->assertFalse($this->falla(RegistrarTrabajoRequest::class, ['tipo' => $tipo, 'descripcion' => 'Trabajo realizado']));
    }

    public static function repuestosInvalidos(): array
    {
        return [
            'cantidad cero' => [[['nombre' => 'Disco', 'cantidad' => 0]]],
            'cantidad negativa' => [[['nombre' => 'Disco', 'cantidad' => -1]]],
            'cantidad decimal' => [[['nombre' => 'Disco', 'cantidad' => 1.5]]],
            'cantidad en texto' => [[['nombre' => 'Disco', 'cantidad' => 'dos']]],
            'cantidad vacia' => [[['nombre' => 'Disco', 'cantidad' => '']]],
            'nombre vacio' => [[['nombre' => '', 'cantidad' => 1]]],
            'nombre de mas de 100 caracteres' => [[['nombre' => str_repeat('x', 101), 'cantidad' => 1]]],
            'campo no permitido' => [[['nombre' => 'Disco', 'cantidad' => 1, 'id_servicio' => 9]]],
            'lista vacia' => [[]],
        ];
    }

    #[DataProvider('repuestosInvalidos')]
    public function test_los_repuestos_invalidos_son_rechazados(array $repuestos): void
    {
        $this->assertTrue($this->falla(RegistrarRepuestosRequest::class, ['repuestos' => $repuestos]));
    }

    public function test_un_repuesto_con_nombre_y_cantidad_valida_es_aceptado(): void
    {
        $this->assertFalse($this->falla(RegistrarRepuestosRequest::class, ['repuestos' => [['nombre' => 'Memoria RAM', 'cantidad' => 2]]]));
    }

    public function test_la_observacion_del_repuesto_es_opcional(): void
    {
        $this->assertFalse($this->falla(RegistrarRepuestosRequest::class, ['repuestos' => [['nombre' => 'Memoria RAM', 'cantidad' => 1, 'observacion' => 'Garantía']]]));
    }

    public function test_el_cambio_de_estado_exige_un_estado(): void
    {
        $this->assertTrue($this->falla(CambiarEstadoRequest::class, ['estado' => '']));
    }

    public function test_el_cambio_de_estado_rechaza_un_estado_ajeno(): void
    {
        $this->assertTrue($this->falla(CambiarEstadoRequest::class, ['estado' => 'cancelado']));
    }

    public static function estadosValidos(): array
    {
        return [['pendiente'], ['en_curso'], ['completado']];
    }

    #[DataProvider('estadosValidos')]
    public function test_el_cambio_de_estado_acepta_los_tres_estados(string $estado): void
    {
        $this->assertFalse($this->falla(CambiarEstadoRequest::class, ['estado' => $estado]));
    }
}
