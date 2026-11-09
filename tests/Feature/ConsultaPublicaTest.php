<?php

namespace Tests\Feature;

use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class ConsultaPublicaTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function servicioConAvance(): Servicio
    {
        $cliente = $this->cliente('Mario Ficticio', '1100200', 'mario@ejemplo.test');
        $equipo = $this->equipo($cliente, 'Dell ficticio');
        $servicio = $this->servicio($equipo, $this->tecnico('Laura Ficticia', '900100'), 'en_curso', [
            'nombre' => 'Revisión general',
            'descripcion_problema' => 'Problema confidencial del cliente',
        ]);
        $this->diagnostico($servicio, 'Detalle técnico reservado');
        $this->trabajo($servicio, 'mantenimiento', 'Actividad técnica reservada');
        $this->repuesto($servicio, 'Repuesto reservado', 3);

        return $servicio;
    }

    private function consultar(string $codigo)
    {
        return $this->from('/')->post('/consultar', ['codigo_seguimiento' => $codigo]);
    }

    public function test_un_visitante_sin_cuenta_abre_la_pagina_de_consulta(): void
    {
        $this->get('/')->assertOk()->assertSee('Código de seguimiento');

        $this->assertGuest();
    }

    public function test_un_codigo_valido_muestra_nombre_computador_y_estado(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)->assertOk()
            ->assertSee('Revisión general')->assertSee($servicio->equipo->codigo_equipo)->assertSee('Dell ficticio')->assertSee('En curso');
    }

    public function test_el_resumen_indica_que_avances_existen(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)
            ->assertSee('Ya hay un diagnóstico registrado.')->assertSee('Se registró una actividad de mantenimiento.')->assertSee('Todavía no hay reparaciones registradas.');
    }

    public function test_la_consulta_no_muestra_datos_personales_del_cliente(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)->assertDontSee('Mario Ficticio')->assertDontSee('1100200')->assertDontSee('mario@ejemplo.test');
    }

    public function test_la_consulta_no_muestra_datos_del_tecnico(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)->assertDontSee('Laura Ficticia')->assertDontSee('900100');
    }

    public function test_la_consulta_no_muestra_detalles_tecnicos(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)
            ->assertDontSee('Detalle técnico reservado')->assertDontSee('Actividad técnica reservada')->assertDontSee('Repuesto reservado')->assertDontSee('Problema confidencial del cliente');
    }

    public function test_la_consulta_no_ofrece_formularios_para_modificar(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar($servicio->codigo_seguimiento)->assertDontSee('/mis-servicios', false)->assertDontSee('/estado', false);
    }

    public function test_un_codigo_vacio_muestra_un_aviso(): void
    {
        $this->consultar('')->assertRedirect('/')->assertSessionHasErrors(['codigo_seguimiento' => 'Escribe el código de seguimiento.']);
    }

    public function test_un_codigo_inexistente_muestra_un_aviso(): void
    {
        $this->servicioConAvance();

        $this->followingRedirects()->post('/consultar', ['codigo_seguimiento' => str_repeat('0', 32)])->assertSee('No encontramos un servicio con ese código.');
    }

    public function test_un_codigo_en_mayusculas_no_coincide(): void
    {
        $servicio = $this->servicioConAvance();

        $this->consultar(strtoupper($servicio->codigo_seguimiento))->assertSessionHasErrors('codigo_seguimiento');
    }

    public function test_un_codigo_con_inyeccion_sql_no_devuelve_servicios(): void
    {
        $this->servicioConAvance();

        $this->consultar("' OR '1'='1")->assertSessionHasErrors('codigo_seguimiento');
    }

    public function test_un_codigo_demasiado_largo_es_rechazado(): void
    {
        $this->consultar(str_repeat('a', 33))->assertSessionHasErrors('codigo_seguimiento');
    }

    public function test_el_codigo_no_viaja_en_la_direccion(): void
    {
        $this->get('/consultar?codigo_seguimiento=abc')->assertStatus(405);
    }

    public function test_el_codigo_consultado_no_se_rellena_tras_un_error(): void
    {
        $this->followingRedirects()->from('/')->post('/consultar', ['codigo_seguimiento' => 'codigo-secreto-x'])->assertDontSee('codigo-secreto-x');
    }

    public function test_cada_codigo_muestra_solo_su_servicio(): void
    {
        $servicio = $this->servicioConAvance();
        $this->servicio(datos: ['nombre' => 'Servicio de otra persona']);

        $this->consultar($servicio->codigo_seguimiento)->assertDontSee('Servicio de otra persona');
    }

    public function test_la_consulta_publica_se_limita_a_treinta_por_minuto(): void
    {
        foreach (range(1, 30) as $intento) {
            $this->post('/consultar', ['codigo_seguimiento' => 'inexistente'.$intento]);
        }

        $this->post('/consultar', ['codigo_seguimiento' => 'inexistente31'])->assertStatus(429);
    }

    public function test_un_servicio_sin_avance_lo_indica(): void
    {
        $servicio = $this->servicio(datos: ['nombre' => 'Recién recibido']);

        $this->consultar($servicio->codigo_seguimiento)->assertSee('Pendiente')->assertSee('Todavía no hay un diagnóstico registrado.');
    }
}
