<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class InicioSesionTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_el_administrador_ingresa_y_llega_a_su_panel(): void
    {
        $administrador = $this->administrador();

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => $this->contrasenaDePrueba])->assertRedirect('/panel');

        $this->assertAuthenticatedAs($administrador);
    }

    public function test_el_panel_del_administrador_muestra_sus_funciones(): void
    {
        $this->actingAs($this->administrador())->get('/panel')
            ->assertOk()->assertSee('Servicios')->assertSee('Nuevo servicio')->assertSee('Clientes')->assertSee('Técnicos')->assertSee('Cuentas');
    }

    public function test_el_tecnico_ingresa_y_llega_a_su_panel(): void
    {
        $tecnico = $this->cuentaTecnico(nombreUsuario: 'tecnica1');

        $this->post('/ingresar', ['nombre_usuario' => 'tecnica1', 'password' => $this->contrasenaDePrueba])->assertRedirect('/panel');

        $this->assertAuthenticatedAs($tecnico);
    }

    public function test_el_panel_del_tecnico_no_muestra_funciones_de_administrador(): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/panel')
            ->assertOk()->assertSee('Mis servicios')->assertDontSee('Nuevo servicio')->assertDontSee('Cuentas');
    }

    public function test_el_usuario_vacio_es_rechazado(): void
    {
        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => '', 'password' => 'algo'])->assertSessionHasErrors('nombre_usuario');

        $this->assertGuest();
    }

    public function test_la_contrasena_vacia_es_rechazada(): void
    {
        $this->administrador();

        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => ''])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_una_contrasena_incorrecta_no_inicia_sesion(): void
    {
        $this->administrador();

        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada'])
            ->assertRedirect('/ingresar')->assertSessionHasErrors('nombre_usuario');

        $this->assertGuest();
    }

    public function test_un_usuario_inexistente_recibe_el_mismo_aviso_que_una_contrasena_incorrecta(): void
    {
        $this->administrador();

        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada']);
        $avisoContrasena = session('errors')->first('nombre_usuario');
        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'nadie', 'password' => 'Clave-equivocada']);

        $this->assertSame($avisoContrasena, session('errors')->first('nombre_usuario'));
    }

    public function test_el_aviso_de_datos_incorrectos_se_muestra_en_la_pagina(): void
    {
        $this->administrador();

        $this->followingRedirects()->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada'])
            ->assertSee('No se pudo iniciar sesión con estos datos.');
    }

    public function test_una_cuenta_sin_activar_no_inicia_sesion(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);

        $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'pendiente1', 'password' => $this->contrasenaDePrueba])
            ->assertSessionHasErrors('nombre_usuario');

        $this->assertGuest();
    }

    public function test_al_ingresar_se_renueva_la_sesion(): void
    {
        $this->administrador();
        $this->get('/ingresar');
        $anterior = session()->getId();

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => $this->contrasenaDePrueba]);

        $this->assertNotSame($anterior, session()->getId());
    }

    public function test_salir_cierra_la_sesion_y_vuelve_al_ingreso(): void
    {
        $this->actingAs($this->administrador())->post('/salir')->assertRedirect('/ingresar');

        $this->assertGuest();
    }

    public function test_despues_de_salir_el_panel_pide_ingresar_nuevamente(): void
    {
        $this->actingAs($this->administrador())->post('/salir');

        $this->get('/panel')->assertRedirect('/ingresar');
    }

    public function test_salir_renueva_el_token_csrf(): void
    {
        $this->actingAs($this->administrador())->get('/panel');
        $anterior = session()->token();

        $this->post('/salir');

        $this->assertNotSame($anterior, session()->token());
    }

    public function test_salir_no_se_puede_hacer_con_un_enlace_get(): void
    {
        $this->actingAs($this->administrador())->get('/salir')->assertStatus(405);
    }

    public function test_cinco_intentos_fallidos_bloquean_temporalmente_el_ingreso(): void
    {
        $this->administrador();

        foreach (range(1, 5) as $intento) {
            $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada']);
        }

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => $this->contrasenaDePrueba])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_el_bloqueo_temporal_indica_cuando_reintentar(): void
    {
        $this->administrador();

        foreach (range(1, 5) as $intento) {
            $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada']);
        }

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada'])->assertHeader('Retry-After');
    }

    public function test_el_bloqueo_temporal_termina_despues_de_un_minuto(): void
    {
        $this->administrador();

        foreach (range(1, 5) as $intento) {
            $this->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'Clave-equivocada']);
        }
        $this->travel(61)->seconds();

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => $this->contrasenaDePrueba])->assertRedirect('/panel');
    }

    public function test_la_pagina_de_ingreso_ofrece_activar_cuenta_y_consultar_servicio(): void
    {
        $this->get('/ingresar')->assertOk()->assertSee('Activar mi cuenta')->assertSee('Consultar un servicio');
    }

    public function test_la_guia_de_activacion_del_ingreso_abre_el_formulario_de_activacion(): void
    {
        preg_match('/<a href="([^"]+)">Activar mi cuenta<\/a>/', $this->get('/ingresar')->getContent(), $enlace);

        $this->assertSame(route('activar'), $enlace[1] ?? null);
        $this->get($enlace[1])->assertOk()->assertSee('name="codigo_activacion"', false);
    }

    public function test_tras_rechazar_una_cuenta_sin_activar_la_pagina_es_igual_a_la_de_un_usuario_inexistente(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);

        $this->assertSame($this->paginaTrasIngresoRechazado('inexistente1'), $this->paginaTrasIngresoRechazado('pendiente1'));
    }

    public function test_tras_rechazar_una_cuenta_sin_activar_la_pagina_es_igual_a_la_de_una_contrasena_incorrecta(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);
        $this->administrador('activo1');

        $this->assertSame($this->paginaTrasIngresoRechazado('activo1'), $this->paginaTrasIngresoRechazado('pendiente1'));
    }

    public function test_una_cuenta_sin_activar_con_su_contrasena_recibe_el_aviso_generico_y_la_guia(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);

        $this->followingRedirects()->from('/ingresar')->post('/ingresar', ['nombre_usuario' => 'pendiente1', 'password' => $this->contrasenaDePrueba])
            ->assertSeeText('No se pudo iniciar sesión con estos datos.')
            ->assertSeeText('Si todavía no activas tu cuenta, usa Activar mi cuenta con el código que te entregó el administrador.');

        $this->assertGuest();
    }

    private function paginaTrasIngresoRechazado(string $nombreUsuario): string
    {
        $html = $this->followingRedirects()->from('/ingresar')
            ->post('/ingresar', ['nombre_usuario' => $nombreUsuario, 'password' => 'Clave-equivocada'])
            ->assertOk()->assertSeeText('No se pudo iniciar sesión con estos datos.')->getContent();

        return preg_replace('/name="_token" value="[^"]*"/', '', str_replace($nombreUsuario, '[usuario]', $html));
    }
}
