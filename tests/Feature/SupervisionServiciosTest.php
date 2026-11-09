<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class SupervisionServiciosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_el_administrador_ve_todos_los_servicios(): void
    {
        $this->servicio(datos: ['nombre' => 'Servicio sin técnico']);
        $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso', datos: ['nombre' => 'Servicio con técnico']);

        $this->actingAs($this->administrador())->get('/servicios')->assertOk()->assertSee('Servicio sin técnico')->assertSee('Servicio con técnico');
    }

    public function test_el_filtro_por_estado_muestra_solo_ese_estado(): void
    {
        $tecnico = $this->tecnico();
        $this->servicio(tecnico: $tecnico, estado: 'completado', datos: ['nombre' => 'Servicio terminado']);
        $this->servicio(tecnico: $tecnico, estado: 'en_curso', datos: ['nombre' => 'Servicio activo']);

        $this->actingAs($this->administrador())->get('/servicios?estado=completado')->assertSee('Servicio terminado')->assertDontSee('Servicio activo');
    }

    public function test_el_filtro_por_tecnico_muestra_solo_sus_servicios(): void
    {
        $laura = $this->tecnico('Laura Ficticia');
        $this->servicio(tecnico: $laura, datos: ['nombre' => 'Servicio de Laura']);
        $this->servicio(tecnico: $this->tecnico('Pedro Ficticio'), datos: ['nombre' => 'Servicio de Pedro']);

        $this->actingAs($this->administrador())->get('/servicios?id_tecnico='.$laura->id_tecnico)->assertSee('Servicio de Laura')->assertDontSee('Servicio de Pedro');
    }

    public function test_el_filtro_sin_tecnico_muestra_los_pendientes_de_asignar(): void
    {
        $this->servicio(datos: ['nombre' => 'Servicio libre']);
        $this->servicio(tecnico: $this->tecnico(), datos: ['nombre' => 'Servicio ocupado']);

        $this->actingAs($this->administrador())->get('/servicios?asignacion=sin_tecnico')->assertSee('Servicio libre')->assertDontSee('Servicio ocupado');
    }

    public function test_el_filtro_con_tecnico_excluye_los_pendientes_de_asignar(): void
    {
        $this->servicio(datos: ['nombre' => 'Servicio libre']);
        $this->servicio(tecnico: $this->tecnico(), datos: ['nombre' => 'Servicio ocupado']);

        $this->actingAs($this->administrador())->get('/servicios?asignacion=con_tecnico')->assertSee('Servicio ocupado')->assertDontSee('Servicio libre');
    }

    public function test_los_filtros_se_pueden_combinar(): void
    {
        $laura = $this->tecnico('Laura Ficticia');
        $this->servicio(tecnico: $laura, estado: 'en_curso', datos: ['nombre' => 'Laura en curso']);
        $this->servicio(tecnico: $laura, datos: ['nombre' => 'Laura pendiente']);

        $this->actingAs($this->administrador())->get('/servicios?estado=en_curso&id_tecnico='.$laura->id_tecnico)->assertSee('Laura en curso')->assertDontSee('Laura pendiente');
    }

    public function test_sin_resultados_se_muestra_un_aviso(): void
    {
        $this->servicio();

        $this->actingAs($this->administrador())->get('/servicios?estado=completado')->assertOk()->assertSee('No hay servicios con esos filtros.');
    }

    public function test_un_filtro_manipulado_es_rechazado(): void
    {
        $this->actingAs($this->administrador())->from('/servicios')->get('/servicios?estado=borrado')->assertSessionHasErrors('estado');
    }

    public function test_un_tecnico_inexistente_en_el_filtro_es_rechazado(): void
    {
        $this->actingAs($this->administrador())->from('/servicios')->get('/servicios?id_tecnico=999999')->assertSessionHasErrors('id_tecnico');
    }

    public function test_el_detalle_muestra_cliente_computador_y_tecnico(): void
    {
        $cliente = $this->cliente('Mario Ficticio', '1100200');
        $equipo = $this->equipo($cliente, 'HP ficticio');
        $servicio = $this->servicio($equipo, $this->tecnico('Laura Ficticia'), 'en_curso', ['nombre' => 'Cambio de batería', 'descripcion_problema' => 'No carga']);

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio)->assertOk()
            ->assertSee('Cambio de batería')->assertSee('No carga')->assertSee('En curso')->assertSee('Mario Ficticio')->assertSee($equipo->codigo_equipo)->assertSee('Laura Ficticia');
    }

    public function test_el_detalle_indica_cuando_no_hay_tecnico(): void
    {
        $servicio = $this->servicio();

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio)->assertOk()->assertSee('Sin técnico asignado');
    }

    public function test_el_detalle_muestra_el_avance_registrado_por_el_tecnico(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');
        $this->diagnostico($servicio, 'Bisagra fracturada');
        $this->trabajo($servicio, 'reparacion', 'Bisagra reemplazada');
        $this->repuesto($servicio, 'Bisagra izquierda', 1);

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio)
            ->assertSee('Bisagra fracturada')->assertSee('Bisagra reemplazada')->assertSee('Bisagra izquierda');
    }

    public function test_el_detalle_no_ofrece_formularios_para_modificar_el_trabajo_tecnico(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio)
            ->assertDontSee('/mis-servicios/'.$servicio->id_servicio.'/diagnosticos', false)
            ->assertDontSee('/mis-servicios/'.$servicio->id_servicio.'/estado', false);
    }

    public function test_un_servicio_inexistente_responde_no_encontrado(): void
    {
        $this->actingAs($this->administrador())->get('/servicios/999999')->assertNotFound();
    }

    public function test_un_tecnico_no_accede_a_la_supervision(): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/servicios')->assertForbidden();
    }
}
