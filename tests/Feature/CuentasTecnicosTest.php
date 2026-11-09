<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class CuentasTecnicosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function crear(array $datos)
    {
        return $this->actingAs($this->administrador())->from('/cuentas/nueva')->post('/cuentas', $datos);
    }

    public function test_el_administrador_crea_la_cuenta_de_un_tecnico_registrado(): void
    {
        $tecnico = $this->tecnico();

        $this->crear(['id_tecnico' => $tecnico->id_tecnico, 'nombre_usuario' => 'laura'])->assertRedirect('/cuentas/nueva')->assertSessionHas('codigo_activacion');

        $this->assertDatabaseHas('usuario', ['nombre_usuario' => 'laura', 'rol' => 'tecnico', 'id_tecnico' => $tecnico->id_tecnico, 'activado' => false]);
    }

    public function test_la_cuenta_nueva_no_tiene_contrasena_hasta_activarse(): void
    {
        $tecnico = $this->tecnico();

        $this->crear(['id_tecnico' => $tecnico->id_tecnico, 'nombre_usuario' => 'laura']);

        $this->assertNull(Usuario::query()->where('nombre_usuario', 'laura')->first()->contrasena_hash);
    }

    public function test_el_codigo_de_activacion_se_muestra_al_administrador(): void
    {
        $tecnico = $this->tecnico();

        $this->followingRedirects()->actingAs($this->administrador())->post('/cuentas', ['id_tecnico' => $tecnico->id_tecnico, 'nombre_usuario' => 'laura'])
            ->assertSee('Entrega este código al técnico')->assertSee(Usuario::query()->where('nombre_usuario', 'laura')->first()->codigo_activacion);
    }

    public function test_el_codigo_generado_activa_la_cuenta_del_tecnico(): void
    {
        $tecnico = $this->tecnico();
        $codigo = $this->crear(['id_tecnico' => $tecnico->id_tecnico, 'nombre_usuario' => 'laura'])->getSession()->get('codigo_activacion');
        $this->post('/salir');

        $this->post('/activar', ['codigo_activacion' => $codigo, 'password' => 'Nueva-clave-1', 'password_confirmation' => 'Nueva-clave-1'])->assertRedirect('/ingresar');

        $this->assertTrue(Usuario::query()->where('nombre_usuario', 'laura')->first()->activado);
    }

    public function test_el_codigo_generado_no_es_predecible(): void
    {
        $codigo = $this->crear(['id_tecnico' => $this->tecnico()->id_tecnico, 'nombre_usuario' => 'laura'])->getSession()->get('codigo_activacion');

        $this->assertGreaterThanOrEqual(32, strlen($codigo));
    }

    public function test_el_tecnico_es_obligatorio(): void
    {
        $this->crear(['nombre_usuario' => 'laura'])->assertSessionHasErrors('id_tecnico');

        $this->assertDatabaseMissing('usuario', ['nombre_usuario' => 'laura']);
    }

    public function test_el_nombre_de_usuario_es_obligatorio(): void
    {
        $this->crear(['id_tecnico' => $this->tecnico()->id_tecnico, 'nombre_usuario' => ''])->assertSessionHasErrors('nombre_usuario');
    }

    public function test_un_nombre_de_usuario_repetido_es_rechazado(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'laura');

        $this->crear(['id_tecnico' => $this->tecnico()->id_tecnico, 'nombre_usuario' => 'laura'])->assertSessionHasErrors('nombre_usuario');

        $this->assertSame(1, Usuario::query()->where('nombre_usuario', 'laura')->count());
    }

    public function test_un_tecnico_con_cuenta_no_recibe_una_segunda(): void
    {
        $tecnico = $this->tecnico();
        $this->cuentaTecnico($tecnico, 'laura');

        $this->crear(['id_tecnico' => $tecnico->id_tecnico, 'nombre_usuario' => 'laura2'])->assertSessionHasErrors('id_tecnico');
    }

    public function test_un_tecnico_inexistente_es_rechazado(): void
    {
        $this->crear(['id_tecnico' => 999999, 'nombre_usuario' => 'laura'])->assertSessionHasErrors('id_tecnico');

        $this->assertDatabaseMissing('usuario', ['nombre_usuario' => 'laura']);
    }

    public function test_la_lista_de_cuentas_muestra_el_estado_pendiente_y_activa(): void
    {
        $this->cuentaTecnico(nombreUsuario: 'activa1');
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);

        $this->actingAs($this->administrador())->get('/cuentas/nueva')->assertOk()->assertSee('Activa')->assertSee('Pendiente de activar');
    }

    public function test_la_lista_de_cuentas_no_expone_contrasenas_ni_codigos(): void
    {
        $cuenta = $this->cuentaTecnico(nombreUsuario: 'activa1');

        $this->actingAs($this->administrador())->get('/cuentas/nueva')
            ->assertDontSee($cuenta->contrasena_hash, false)->assertDontSee($cuenta->codigo_activacion, false);
    }
}
