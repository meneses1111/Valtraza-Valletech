<?php

namespace Tests\Feature;

use App\Models\Tecnico;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_cuenta_inactiva_no_puede_iniciar_sesion(): void
    {
        Usuario::query()->create(['nombre_usuario' => 'admin', 'rol' => 'administrador', 'codigo_activacion' => 'ACT-1']);

        $this->post('/ingresar', ['nombre_usuario' => 'admin', 'password' => 'contrasena123'])
            ->assertSessionHasErrors('nombre_usuario');

        $this->assertGuest();
    }

    public function test_el_codigo_de_activacion_solo_funciona_una_vez(): void
    {
        Usuario::query()->create(['nombre_usuario' => 'admin', 'rol' => 'administrador', 'codigo_activacion' => 'ACT-1']);
        $datos = ['codigo_activacion' => 'ACT-1', 'password' => 'contrasena123', 'password_confirmation' => 'contrasena123'];

        $this->post('/activar', $datos)->assertRedirect('/ingresar');
        $this->post('/activar', $datos)->assertSessionHasErrors('codigo_activacion');

        $this->assertTrue(Usuario::query()->where('nombre_usuario', 'admin')->first()->activado);
    }

    public function test_un_tecnico_no_puede_crear_cuentas(): void
    {
        $tecnico = Tecnico::query()->create(['nombre' => 'Ana', 'numero_identificacion' => '1001']);
        $usuario = Usuario::query()->create([
            'nombre_usuario' => 'ana', 'rol' => 'tecnico', 'id_tecnico' => $tecnico->id_tecnico,
            'codigo_activacion' => 'ACT-ANA',
        ]);
        $usuario->activado = true;
        $usuario->contrasena_hash = Hash::make('contrasena123');
        $usuario->save();

        $this->actingAs($usuario)->get('/cuentas/nueva')->assertForbidden();
    }

    public function test_un_visitante_no_puede_crear_cuentas(): void
    {
        $this->get('/cuentas/nueva')->assertRedirect('/ingresar');
    }
}
