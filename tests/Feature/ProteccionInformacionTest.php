<?php

namespace Tests\Feature;

use App\Models\Servicio;
use App\Support\GuardarRegistro;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class ProteccionInformacionTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_un_error_a_mitad_de_una_operacion_no_deja_registros_incompletos(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');

        try {
            GuardarRegistro::ejecutar(function () use ($servicio): void {
                $this->repuesto($servicio, 'Primera fila');
                throw new RuntimeException('Interrupción simulada');
            });
        } catch (RuntimeException) {
        }

        $this->assertDatabaseCount('repuesto', 0);
    }

    public function test_un_duplicado_detectado_por_la_base_se_informa_como_aviso(): void
    {
        $this->cliente('Mario', '1100200');

        $this->expectException(ValidationException::class);

        GuardarRegistro::ejecutar(fn () => $this->cliente('Otro', '1100200'));
    }

    public function test_la_base_impide_completar_un_servicio_sin_tecnico(): void
    {
        $this->expectException(QueryException::class);

        $this->servicio(estado: 'completado');
    }

    public function test_la_base_impide_repuestos_con_cantidad_cero(): void
    {
        $servicio = $this->servicio();

        $this->expectException(QueryException::class);

        $this->repuesto($servicio, 'Cable', 0);
    }

    public function test_la_base_impide_borrar_un_cliente_con_equipos(): void
    {
        $equipo = $this->equipo();

        $this->expectException(QueryException::class);

        DB::table('cliente')->where('id_cliente', $equipo->id_cliente)->delete();
    }

    public function test_la_base_impide_borrar_un_servicio_con_diagnosticos(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');
        $this->diagnostico($servicio);

        $this->expectException(QueryException::class);

        DB::table('servicio')->where('id_servicio', $servicio->id_servicio)->delete();
    }

    public function test_la_base_impide_codigos_de_seguimiento_repetidos(): void
    {
        $primero = $this->servicio();

        $this->expectException(QueryException::class);

        $this->servicio(datos: ['codigo_seguimiento' => $primero->codigo_seguimiento]);
    }

    public function test_la_base_rechaza_un_estado_fuera_del_enum(): void
    {
        $servicio = $this->servicio();

        $this->expectException(QueryException::class);

        DB::table('servicio')->where('id_servicio', $servicio->id_servicio)->update(['estado' => 'cancelado']);
    }

    public function test_un_texto_con_html_se_muestra_escapado_al_administrador(): void
    {
        $this->cliente('<script>alert(1)</script>', '1100200');

        $this->actingAs($this->administrador())->get('/clientes')
            ->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_un_diagnostico_con_html_se_muestra_escapado_en_la_supervision(): void
    {
        $servicio = $this->servicio(tecnico: $this->tecnico(), estado: 'en_curso');
        $this->diagnostico($servicio, '<img src=x onerror=alert(1)>');

        $this->actingAs($this->administrador())->get('/servicios/'.$servicio->id_servicio)->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_un_nombre_de_servicio_con_html_se_muestra_escapado_en_la_consulta(): void
    {
        $servicio = $this->servicio(datos: ['nombre' => '<b>Servicio</b>']);

        $this->post('/consultar', ['codigo_seguimiento' => $servicio->codigo_seguimiento])->assertDontSee('<b>Servicio</b>', false);
    }

    public function test_los_formularios_incluyen_el_token_csrf(): void
    {
        $this->get('/')->assertSee('name="_token"', false);
        $this->get('/ingresar')->assertSee('name="_token"', false);
        $this->get('/activar')->assertSee('name="_token"', false);
    }

    public function test_las_paginas_incluyen_las_cabeceras_de_seguridad(): void
    {
        $this->get('/')->assertHeader('X-Frame-Options', 'DENY')->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_la_cookie_de_sesion_no_es_accesible_desde_javascript(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
    }

    public function test_los_registros_previos_no_cambian_al_registrar_un_servicio_nuevo(): void
    {
        $equipo = $this->equipo();
        $anterior = $this->servicio($equipo, $this->tecnico(), 'completado');
        $this->diagnostico($anterior, 'Diagnóstico histórico');
        $antes = Servicio::query()->find($anterior->id_servicio)->toArray();

        $this->servicio($equipo);

        $this->assertSame($antes, Servicio::query()->find($anterior->id_servicio)->toArray());
        $this->assertDatabaseHas('diagnostico', ['id_servicio' => $anterior->id_servicio, 'resultado' => 'Diagnóstico histórico']);
    }
}
