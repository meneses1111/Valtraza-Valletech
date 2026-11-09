<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class AsignacionTecnicosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function asignar(int $idServicio, array $datos)
    {
        return $this->actingAs($this->administrador())->from('/servicios/'.$idServicio.'/asignar')->post('/servicios/'.$idServicio.'/asignar', $datos);
    }

    public function test_el_administrador_asigna_un_tecnico_a_un_servicio(): void
    {
        $servicio = $this->servicio();
        $tecnico = $this->tecnico();

        $this->asignar($servicio->id_servicio, ['id_tecnico' => $tecnico->id_tecnico])->assertRedirect('/servicios/'.$servicio->id_servicio);

        $this->assertSame($tecnico->id_tecnico, $servicio->fresh()->id_tecnico);
    }

    public function test_el_servicio_asignado_aparece_entre_los_trabajos_del_tecnico(): void
    {
        $servicio = $this->servicio(datos: ['nombre' => 'Servicio para Laura']);
        $cuenta = $this->cuentaTecnico();
        $this->asignar($servicio->id_servicio, ['id_tecnico' => $cuenta->id_tecnico]);

        $this->actingAs($cuenta)->get('/mis-servicios')->assertSee('Servicio para Laura');
    }

    public function test_asignar_sin_elegir_tecnico_es_rechazado(): void
    {
        $servicio = $this->servicio();

        $this->asignar($servicio->id_servicio, ['id_tecnico' => ''])->assertSessionHasErrors('id_tecnico');

        $this->assertNull($servicio->fresh()->id_tecnico);
    }

    public function test_asignar_un_tecnico_inexistente_es_rechazado(): void
    {
        $servicio = $this->servicio();

        $this->asignar($servicio->id_servicio, ['id_tecnico' => 999999])->assertSessionHasErrors('id_tecnico');

        $this->assertNull($servicio->fresh()->id_tecnico);
    }

    public function test_asignar_a_un_servicio_inexistente_responde_no_encontrado(): void
    {
        $this->asignar(999999, ['id_tecnico' => $this->tecnico()->id_tecnico])->assertNotFound();
    }

    public function test_un_tecnico_puede_tener_varios_servicios(): void
    {
        $tecnico = $this->tecnico();
        $primero = $this->servicio();
        $segundo = $this->servicio();

        $this->asignar($primero->id_servicio, ['id_tecnico' => $tecnico->id_tecnico]);
        $this->asignar($segundo->id_servicio, ['id_tecnico' => $tecnico->id_tecnico]);

        $this->assertSame($tecnico->id_tecnico, $primero->fresh()->id_tecnico);
        $this->assertSame($tecnico->id_tecnico, $segundo->fresh()->id_tecnico);
    }

    public function test_la_reasignacion_conserva_el_estado(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');

        $this->asignar($servicio->id_servicio, ['id_tecnico' => $this->tecnico('Nuevo')->id_tecnico]);

        $this->assertSame('en_curso', $servicio->fresh()->estado);
    }

    public function test_la_reasignacion_conserva_diagnosticos_actividades_y_repuestos(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');
        $this->diagnostico($servicio);
        $this->trabajo($servicio);
        $this->repuesto($servicio);

        $this->asignar($servicio->id_servicio, ['id_tecnico' => $this->tecnico('Nuevo')->id_tecnico]);

        $this->assertDatabaseCount('diagnostico', 1);
        $this->assertDatabaseCount('trabajo', 1);
        $this->assertDatabaseCount('repuesto', 1);
    }

    public function test_el_servicio_reasignado_deja_de_aparecer_al_tecnico_anterior(): void
    {
        $anterior = $this->cuentaTecnico();
        $servicio = $this->servicio(tecnico: $this->tecnicoDe($anterior), datos: ['nombre' => 'Servicio movido']);

        $this->asignar($servicio->id_servicio, ['id_tecnico' => $this->tecnico('Nuevo')->id_tecnico]);

        $this->actingAs($anterior)->get('/mis-servicios')->assertDontSee('Servicio movido');
        $this->actingAs($anterior)->get('/mis-servicios/'.$servicio->id_servicio)->assertForbidden();
    }

    public function test_el_nuevo_tecnico_ve_los_registros_del_anterior(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');
        $this->diagnostico($servicio, 'Batería inflada detectada');
        $nuevo = $this->cuentaTecnico();

        $this->asignar($servicio->id_servicio, ['id_tecnico' => $nuevo->id_tecnico]);

        $this->actingAs($nuevo)->get('/mis-servicios/'.$servicio->id_servicio)->assertOk()->assertSee('Batería inflada detectada');
    }

    public function test_la_pantalla_de_asignacion_lista_los_tecnicos_registrados(): void
    {
        $servicio = $this->servicio();
        $this->tecnico('Laura Ficticia');
        $this->tecnico('Pedro Ficticio');

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio.'/asignar')->assertOk()->assertSee('Laura Ficticia')->assertSee('Pedro Ficticio');
    }
}
