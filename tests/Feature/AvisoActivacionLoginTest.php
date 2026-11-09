<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class AvisoActivacionLoginTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_el_ingreso_explica_como_activar_la_cuenta_antes_de_identificar_al_usuario(): void
    {
        $this->get('/ingresar')->assertOk()
            ->assertSeeText('Si todavía no activas tu cuenta, usa')
            ->assertSeeText('con el código que te entregó el administrador.')
            ->assertSee('href="'.route('activar').'"', false);
    }

    public static function intentosRechazados(): array
    {
        return [
            'cuenta pendiente de activar' => ['pendiente1'],
            'cuenta activa con contraseña incorrecta' => ['activo1'],
            'usuario inexistente' => ['nadie'],
        ];
    }

    #[DataProvider('intentosRechazados')]
    public function test_los_ingresos_rechazados_mantienen_el_aviso_generico_y_la_guia_de_activacion(string $nombreUsuario): void
    {
        $this->cuentaTecnico(nombreUsuario: 'pendiente1', activa: false);
        $this->administrador('activo1');

        $this->from('/ingresar')->post('/ingresar', [
            'nombre_usuario' => $nombreUsuario,
            'password' => 'Clave-equivocada',
        ])->assertRedirect('/ingresar')->assertSessionHasErrors([
            'nombre_usuario' => 'No se pudo iniciar sesión con estos datos.',
        ]);

        $this->assertGuest();
        $this->get('/ingresar')->assertOk()
            ->assertSeeText('No se pudo iniciar sesión con estos datos.')
            ->assertSeeText('Si todavía no activas tu cuenta, usa')
            ->assertSeeText('con el código que te entregó el administrador.')
            ->assertSee('href="'.route('activar').'"', false);
    }
}
