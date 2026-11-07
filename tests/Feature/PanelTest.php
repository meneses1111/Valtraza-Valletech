<?php

namespace Tests\Feature;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelTest extends TestCase
{
    public static function roles(): array
    {
        return [['administrador'], ['tecnico']];
    }

    #[DataProvider('roles')]
    public function test_un_usuario_autenticado_que_abre_ingresar_es_redirigido_al_panel(string $rol): void
    {
        $usuario = new Usuario(['nombre_usuario' => 'prueba', 'rol' => $rol]);

        $this->actingAs($usuario);

        $this->get('/ingresar')->assertRedirect('/panel');
    }

    #[DataProvider('roles')]
    public function test_un_usuario_autenticado_que_abre_activar_es_redirigido_al_panel(string $rol): void
    {
        $usuario = new Usuario(['nombre_usuario' => 'prueba', 'rol' => $rol]);

        $this->actingAs($usuario);

        $this->get('/activar')->assertRedirect('/panel');
    }

    #[DataProvider('roles')]
    public function test_un_usuario_autenticado_puede_ver_el_panel(string $rol): void
    {
        $usuario = new Usuario(['nombre_usuario' => 'prueba', 'rol' => $rol]);

        $this->actingAs($usuario);

        $this->get('/panel')->assertOk()->assertSee('Bienvenido, prueba');
    }

    public function test_un_visitante_debe_ingresar_antes_de_ver_el_panel(): void
    {
        $this->get('/panel')->assertRedirect('/ingresar');
    }
}
