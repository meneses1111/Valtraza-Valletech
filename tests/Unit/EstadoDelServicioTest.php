<?php

namespace Tests\Unit;

use App\Enums\EstadoServicio;
use App\Models\Servicio;
use App\States\Servicios\Completado;
use App\States\Servicios\EnCurso;
use App\States\Servicios\EstadoDelServicio;
use App\States\Servicios\Pendiente;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EstadoDelServicioTest extends TestCase
{
    public function test_cada_estado_del_servicio_tiene_su_regla(): void
    {
        $this->assertInstanceOf(Pendiente::class, EstadoDelServicio::para(EstadoServicio::Pendiente));
        $this->assertInstanceOf(EnCurso::class, EstadoDelServicio::para(EstadoServicio::EnCurso));
        $this->assertInstanceOf(Completado::class, EstadoDelServicio::para(EstadoServicio::Completado));
    }

    public function test_completar_sin_diagnostico_es_rechazado(): void
    {
        $this->expectException(ValidationException::class);

        (new Completado)->establecer(new Servicio(['estado' => 'en_curso']), false);
    }

    public function test_completar_sin_diagnostico_conserva_el_estado_anterior(): void
    {
        $servicio = new Servicio(['estado' => 'en_curso']);

        try {
            (new Completado)->establecer($servicio, false);
        } catch (ValidationException) {
        }

        $this->assertSame('en_curso', $servicio->estado);
    }

    public function test_el_aviso_de_completar_sin_diagnostico_pide_registrarlo(): void
    {
        try {
            (new Completado)->establecer(new Servicio(['estado' => 'pendiente']), false);
            $this->fail('Se permitió completar sin diagnóstico.');
        } catch (ValidationException $excepcion) {
            $this->assertStringContainsString('diagnóstico', $excepcion->errors()['estado'][0]);
        }
    }

    public function test_completar_con_diagnostico_cambia_el_estado(): void
    {
        $servicio = new Servicio(['estado' => 'en_curso']);

        (new Completado)->establecer($servicio, true);

        $this->assertSame('completado', $servicio->estado);
    }

    public function test_pasar_a_en_curso_no_exige_diagnostico(): void
    {
        $servicio = new Servicio(['estado' => 'pendiente']);

        (new EnCurso)->establecer($servicio, false);

        $this->assertSame('en_curso', $servicio->estado);
    }

    public function test_volver_a_pendiente_no_exige_diagnostico(): void
    {
        $servicio = new Servicio(['estado' => 'en_curso']);

        (new Pendiente)->establecer($servicio, false);

        $this->assertSame('pendiente', $servicio->estado);
    }
}
