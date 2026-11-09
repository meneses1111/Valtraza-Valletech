<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class ServiciosAsignadosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_el_tecnico_solo_ve_sus_servicios_asignados(): void
    {
        $cuenta = $this->cuentaTecnico();
        $this->servicio(tecnico: $this->tecnicoDe($cuenta), datos: ['nombre' => 'Servicio propio']);
        $this->servicio(tecnico: $this->tecnico('Otro'), datos: ['nombre' => 'Servicio ajeno']);
        $this->servicio(datos: ['nombre' => 'Servicio sin asignar']);

        $this->actingAs($cuenta)->get('/mis-servicios')->assertOk()->assertSee('Servicio propio')->assertDontSee('Servicio ajeno')->assertDontSee('Servicio sin asignar');
    }

    public function test_los_servicios_se_ordenan_del_mas_reciente_al_mas_antiguo(): void
    {
        $cuenta = $this->cuentaTecnico();
        $tecnico = $this->tecnicoDe($cuenta);
        $this->servicio(tecnico: $tecnico, datos: ['nombre' => 'Servicio de octubre', 'fecha' => '2026-10-01']);
        $this->servicio(tecnico: $tecnico, datos: ['nombre' => 'Servicio de noviembre', 'fecha' => '2026-11-08']);
        $this->servicio(tecnico: $tecnico, datos: ['nombre' => 'Servicio de septiembre', 'fecha' => '2026-09-15']);

        $this->actingAs($cuenta)->get('/mis-servicios')->assertSeeInOrder(['Servicio de noviembre', 'Servicio de octubre', 'Servicio de septiembre']);
    }

    public function test_el_tecnico_filtra_sus_servicios_por_estado(): void
    {
        $cuenta = $this->cuentaTecnico();
        $tecnico = $this->tecnicoDe($cuenta);
        $this->servicio(tecnico: $tecnico, estado: 'en_curso', datos: ['nombre' => 'Servicio en curso']);
        $this->servicio(tecnico: $tecnico, datos: ['nombre' => 'Servicio pendiente']);

        $this->actingAs($cuenta)->get('/mis-servicios?estado=en_curso')->assertSee('Servicio en curso')->assertDontSee('Servicio pendiente');
    }

    public function test_un_tecnico_sin_servicios_recibe_un_aviso(): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/mis-servicios')->assertOk()->assertSee('Todavía no tienes servicios asignados.');
    }

    public function test_un_filtro_sin_resultados_muestra_un_aviso(): void
    {
        $cuenta = $this->cuentaTecnico();
        $this->servicio(tecnico: $this->tecnicoDe($cuenta));

        $this->actingAs($cuenta)->get('/mis-servicios?estado=completado')->assertOk()->assertSee('No hay servicios con ese filtro.');
    }

    public function test_un_estado_de_filtro_inexistente_es_rechazado(): void
    {
        $this->actingAs($this->cuentaTecnico())->from('/mis-servicios')->get('/mis-servicios?estado=cancelado')->assertSessionHasErrors('estado');
    }

    public function test_el_detalle_muestra_nombre_descripcion_estado_y_computador(): void
    {
        $cuenta = $this->cuentaTecnico();
        $equipo = $this->equipo(modelo: 'Asus ficticio');
        $servicio = $this->servicio($equipo, $this->tecnicoDe($cuenta), 'en_curso', ['nombre' => 'Cambio de disco', 'descripcion_problema' => 'Arranque lento']);

        $this->actingAs($cuenta)->get('/mis-servicios/'.$servicio->id_servicio)->assertOk()
            ->assertSee('Cambio de disco')->assertSee('Arranque lento')->assertSee('En curso')->assertSee($equipo->codigo_equipo)->assertSee('Asus ficticio');
    }

    public function test_el_detalle_muestra_lo_registrado_durante_el_trabajo(): void
    {
        $cuenta = $this->cuentaTecnico();
        $servicio = $this->servicio(tecnico: $this->tecnicoDe($cuenta), estado: 'en_curso');
        $this->diagnostico($servicio, 'Memoria defectuosa');
        $this->trabajo($servicio, 'reparacion', 'Reemplazo de memoria');
        $this->repuesto($servicio, 'Módulo RAM 8 GB', 2);

        $this->actingAs($cuenta)->get('/mis-servicios/'.$servicio->id_servicio)->assertSee('Memoria defectuosa')->assertSee('Reemplazo de memoria')->assertSee('Módulo RAM 8 GB');
    }

    public function test_el_historial_muestra_servicios_completados_anteriores_del_computador(): void
    {
        $cuenta = $this->cuentaTecnico();
        $equipo = $this->equipo();
        $anterior = $this->servicio($equipo, $this->tecnico('Anterior'), 'completado', ['nombre' => 'Limpieza de 2025', 'fecha' => '2025-05-01']);
        $this->trabajo($anterior, 'mantenimiento', 'Limpieza y pasta térmica');
        $actual = $this->servicio($equipo, $this->tecnicoDe($cuenta), 'en_curso');

        $this->actingAs($cuenta)->get('/mis-servicios/'.$actual->id_servicio)->assertSee('Limpieza de 2025')->assertSee('Limpieza y pasta térmica');
    }

    public function test_el_historial_no_incluye_servicios_sin_finalizar_de_otros_tecnicos(): void
    {
        $cuenta = $this->cuentaTecnico();
        $equipo = $this->equipo();
        $this->servicio($equipo, $this->tecnico('Otro'), 'en_curso', ['nombre' => 'Trabajo ajeno sin terminar']);
        $actual = $this->servicio($equipo, $this->tecnicoDe($cuenta), 'en_curso');

        $this->actingAs($cuenta)->get('/mis-servicios/'.$actual->id_servicio)->assertDontSee('Trabajo ajeno sin terminar');
    }

    public function test_el_tecnico_no_puede_abrir_un_servicio_ajeno(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico('Otro'));

        $this->actingAs($this->cuentaTecnico())->get('/mis-servicios/'.$servicio->id_servicio)->assertForbidden()->assertSee('Acceso no autorizado');
    }

    public function test_el_tecnico_no_puede_abrir_un_servicio_sin_asignar(): void
    {
        $servicio = $this->servicio();

        $this->actingAs($this->cuentaTecnico())->get('/mis-servicios/'.$servicio->id_servicio)->assertForbidden();
    }

    public function test_un_servicio_inexistente_no_revela_informacion(): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/mis-servicios/999999')->assertStatus(404);
    }

    public function test_el_administrador_no_usa_la_lista_del_tecnico(): void
    {
        $this->actingAs($this->administrador())->get('/mis-servicios')->assertForbidden();
    }
}
