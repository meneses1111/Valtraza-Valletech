<?php

namespace Tests\Unit;

use App\Enums\EstadoServicio;
use App\Enums\TipoTrabajo;
use Tests\TestCase;

class EnumeracionesTest extends TestCase
{
    public function test_el_servicio_solo_tiene_los_tres_estados_de_la_ers(): void
    {
        $this->assertSame(['pendiente', 'en_curso', 'completado'], array_column(EstadoServicio::cases(), 'value'));
    }

    public function test_los_estados_se_muestran_con_texto_legible(): void
    {
        $this->assertSame(['Pendiente', 'En curso', 'Completado'], array_map(fn ($estado) => $estado->etiqueta(), EstadoServicio::cases()));
    }

    public function test_las_actividades_son_mantenimiento_o_reparacion(): void
    {
        $this->assertSame(['mantenimiento', 'reparacion'], array_column(TipoTrabajo::cases(), 'value'));
    }

    public function test_las_actividades_se_muestran_con_texto_legible(): void
    {
        $this->assertSame(['Mantenimiento', 'Reparación'], array_map(fn ($tipo) => $tipo->etiqueta(), TipoTrabajo::cases()));
    }
}
