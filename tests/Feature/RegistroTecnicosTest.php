<?php

namespace Tests\Feature;

use App\Models\Tecnico;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RegistroTecnicosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function registrar(array $datos)
    {
        return $this->actingAs($this->administrador())->from('/tecnicos')->post('/tecnicos', $datos);
    }

    public function test_el_administrador_registra_un_tecnico_con_nombre_e_identificacion(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100'])->assertRedirect('/tecnicos')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tecnico', ['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100', 'correo' => null]);
    }

    public function test_el_correo_del_tecnico_es_opcional_y_se_guarda_si_se_envia(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100', 'correo' => 'laura@ejemplo.test']);

        $this->assertDatabaseHas('tecnico', ['numero_identificacion' => '900100', 'correo' => 'laura@ejemplo.test']);
    }

    public function test_el_nombre_del_tecnico_es_obligatorio(): void
    {
        $this->registrar(['nombre' => '', 'numero_identificacion' => '900100'])->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('tecnico', 0);
    }

    public function test_la_identificacion_del_tecnico_es_obligatoria(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => ''])->assertSessionHasErrors('numero_identificacion');

        $this->assertDatabaseCount('tecnico', 0);
    }

    public function test_un_nombre_de_solo_espacios_es_rechazado(): void
    {
        $this->registrar(['nombre' => '   ', 'numero_identificacion' => '900100'])->assertSessionHasErrors('nombre');
    }

    public function test_una_identificacion_repetida_no_crea_otro_tecnico(): void
    {
        $this->tecnico('Primero', '900100');

        $this->registrar(['nombre' => 'Segundo', 'numero_identificacion' => '900100'])->assertSessionHasErrors('numero_identificacion');

        $this->assertSame(1, Tecnico::query()->where('numero_identificacion', '900100')->count());
    }

    public function test_un_correo_invalido_es_rechazado(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100', 'correo' => 'no-es-correo'])->assertSessionHasErrors('correo');
    }

    public function test_registrar_un_tecnico_no_crea_una_cuenta_de_acceso(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100']);

        $this->assertSame(0, Usuario::query()->where('rol', 'tecnico')->count());
    }

    public function test_el_tecnico_registrado_aparece_en_la_lista(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100']);

        $this->get('/tecnicos')->assertOk()->assertSee('Laura Ficticia')->assertSee('900100');
    }

    public function test_el_tecnico_registrado_puede_asignarse_a_un_servicio(): void
    {
        $this->registrar(['nombre' => 'Laura Ficticia', 'numero_identificacion' => '900100']);
        $servicio = $this->servicio();

        $this->get('/servicios/'.$servicio->id_servicio.'/asignar')->assertSee('Laura Ficticia');
    }
}
