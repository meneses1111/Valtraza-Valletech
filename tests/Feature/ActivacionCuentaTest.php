<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class ActivacionCuentaTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function cuentaPendiente(string $codigo = 'CODIGO-FICTICIO-1'): Usuario
    {
        $usuario = $this->cuentaTecnico(nombreUsuario: 'nueva1', activa: false);
        $usuario->codigo_activacion = $codigo;
        $usuario->save();

        return $usuario;
    }

    private function activar(string $codigo, string $contrasena = 'Nueva-clave-1', ?string $confirmacion = null)
    {
        return $this->from('/activar')->post('/activar', [
            'codigo_activacion' => $codigo,
            'password' => $contrasena,
            'password_confirmation' => $confirmacion ?? $contrasena,
        ]);
    }

    public function test_un_codigo_valido_activa_la_cuenta(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-FICTICIO-1')->assertRedirect('/ingresar');

        $this->assertTrue($usuario->fresh()->activado);
    }

    public function test_la_activacion_informa_que_ya_puede_ingresar(): void
    {
        $this->cuentaPendiente();

        $this->followingRedirects()->post('/activar', ['codigo_activacion' => 'CODIGO-FICTICIO-1', 'password' => 'Nueva-clave-1', 'password_confirmation' => 'Nueva-clave-1'])
            ->assertSee('Cuenta activada. Ya puedes ingresar.');
    }

    public function test_despues_de_activar_puede_iniciar_sesion_con_su_contrasena(): void
    {
        $usuario = $this->cuentaPendiente();
        $this->activar('CODIGO-FICTICIO-1');

        $this->post('/ingresar', ['nombre_usuario' => 'nueva1', 'password' => 'Nueva-clave-1'])->assertRedirect('/panel');

        $this->assertAuthenticatedAs($usuario->fresh());
    }

    public function test_el_codigo_no_puede_usarse_dos_veces(): void
    {
        $this->cuentaPendiente();
        $this->activar('CODIGO-FICTICIO-1');

        $this->activar('CODIGO-FICTICIO-1', 'Otra-clave-22')->assertSessionHasErrors('codigo_activacion');
    }

    public function test_un_segundo_uso_del_codigo_no_cambia_la_contrasena(): void
    {
        $usuario = $this->cuentaPendiente();
        $this->activar('CODIGO-FICTICIO-1');
        $this->activar('CODIGO-FICTICIO-1', 'Otra-clave-22');

        $this->assertTrue(Hash::check('Nueva-clave-1', $usuario->fresh()->contrasena_hash));
    }

    public function test_el_codigo_usado_se_reemplaza_en_la_base(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-FICTICIO-1');

        $this->assertNotSame('CODIGO-FICTICIO-1', $usuario->fresh()->codigo_activacion);
    }

    public function test_un_codigo_incorrecto_no_activa_la_cuenta(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-EQUIVOCADO')->assertSessionHasErrors('codigo_activacion');

        $this->assertFalse($usuario->fresh()->activado);
    }

    public function test_el_aviso_de_codigo_incorrecto_se_muestra_en_la_pagina(): void
    {
        $this->cuentaPendiente();

        $this->followingRedirects()->from('/activar')->post('/activar', ['codigo_activacion' => 'CODIGO-EQUIVOCADO', 'password' => 'Nueva-clave-1', 'password_confirmation' => 'Nueva-clave-1'])
            ->assertSee('Código inválido o ya utilizado.');
    }

    public function test_el_codigo_distingue_mayusculas_y_minusculas(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('codigo-ficticio-1')->assertSessionHasErrors('codigo_activacion');

        $this->assertFalse($usuario->fresh()->activado);
    }

    public function test_una_contrasena_de_siete_caracteres_no_activa_la_cuenta(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-FICTICIO-1', 'Corta-1')->assertSessionHasErrors('password');

        $this->assertFalse($usuario->fresh()->activado);
    }

    public function test_una_contrasena_rechazada_no_consume_el_codigo(): void
    {
        $usuario = $this->cuentaPendiente();
        $this->activar('CODIGO-FICTICIO-1', 'Corta-1');

        $this->activar('CODIGO-FICTICIO-1')->assertRedirect('/ingresar');

        $this->assertTrue($usuario->fresh()->activado);
    }

    public function test_la_confirmacion_distinta_no_activa_la_cuenta(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-FICTICIO-1', 'Nueva-clave-1', 'Nueva-clave-2')->assertSessionHasErrors('password');

        $this->assertFalse($usuario->fresh()->activado);
    }

    public function test_la_contrasena_se_guarda_con_hash(): void
    {
        $usuario = $this->cuentaPendiente();

        $this->activar('CODIGO-FICTICIO-1');

        $this->assertNotSame('Nueva-clave-1', $usuario->fresh()->contrasena_hash);
        $this->assertTrue(Hash::check('Nueva-clave-1', $usuario->fresh()->contrasena_hash));
    }

    public function test_cinco_activaciones_fallidas_bloquean_temporalmente_el_formulario(): void
    {
        $this->cuentaPendiente();

        foreach (range(1, 5) as $intento) {
            $this->activar('CODIGO-EQUIVOCADO-'.$intento);
        }

        $this->activar('CODIGO-FICTICIO-1')->assertStatus(429);
    }

    public function test_el_formulario_de_activacion_no_conserva_el_codigo_tras_un_error(): void
    {
        $this->cuentaPendiente();

        $this->followingRedirects()->from('/activar')->post('/activar', ['codigo_activacion' => 'CODIGO-EQUIVOCADO', 'password' => 'Nueva-clave-1', 'password_confirmation' => 'Nueva-clave-1'])
            ->assertDontSee('value="CODIGO-EQUIVOCADO"', false);
    }
}
