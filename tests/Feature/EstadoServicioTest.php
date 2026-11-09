<?php

namespace Tests\Feature;

use App\Models\Servicio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class EstadoServicioTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private Usuario $cuenta;

    private Servicio $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cuenta = $this->cuentaTecnico();
        $this->servicio = $this->servicio(tecnico: $this->tecnicoDe($this->cuenta));
    }

    private function cambiar(array $datos)
    {
        $ruta = '/mis-servicios/'.$this->servicio->id_servicio.'/estado';

        return $this->actingAs($this->cuenta)->from($ruta)->post($ruta, $datos);
    }

    public function test_el_tecnico_pasa_el_servicio_a_en_curso(): void
    {
        $this->cambiar(['estado' => 'en_curso'])->assertRedirect('/mis-servicios/'.$this->servicio->id_servicio);

        $this->assertSame('en_curso', $this->servicio->fresh()->estado);
    }

    public function test_sin_elegir_estado_no_se_guarda(): void
    {
        $this->cambiar(['estado' => ''])->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $this->servicio->fresh()->estado);
    }

    public function test_un_estado_ajeno_no_se_guarda(): void
    {
        $this->cambiar(['estado' => 'cancelado'])->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $this->servicio->fresh()->estado);
    }

    public function test_completar_sin_diagnostico_es_rechazado(): void
    {
        $this->cambiar(['estado' => 'completado'])->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $this->servicio->fresh()->estado);
    }

    public function test_el_aviso_de_completar_sin_diagnostico_se_muestra(): void
    {
        $ruta = '/mis-servicios/'.$this->servicio->id_servicio.'/estado';

        $this->actingAs($this->cuenta)->followingRedirects()->from($ruta)->post($ruta, ['estado' => 'completado'])
            ->assertSee('No puedes completar el servicio sin registrar al menos un diagnóstico.');
    }

    public function test_completar_con_diagnostico_y_sin_reparacion_es_aceptado(): void
    {
        $this->diagnostico($this->servicio, 'No se encontraron fallas');

        $this->cambiar(['estado' => 'completado'])->assertSessionHasNoErrors();

        $this->assertSame('completado', $this->servicio->fresh()->estado);
        $this->assertDatabaseCount('trabajo', 0);
    }

    public function test_el_estado_nuevo_se_ve_en_la_supervision(): void
    {
        $this->cambiar(['estado' => 'en_curso']);

        $this->actingAs($this->administrador())->get('/servicios/'.$this->servicio->id_servicio)->assertSee('En curso');
    }

    public function test_el_estado_nuevo_se_ve_en_la_consulta_publica(): void
    {
        $this->cambiar(['estado' => 'en_curso']);
        $this->post('/salir');

        $this->post('/consultar', ['codigo_seguimiento' => $this->servicio->codigo_seguimiento])->assertSee('En curso');
    }

    public function test_el_estado_se_conserva_al_reasignar(): void
    {
        $this->cambiar(['estado' => 'en_curso']);
        $nuevo = $this->tecnico('Nuevo encargado');

        $this->actingAs($this->administrador())->post('/servicios/'.$this->servicio->id_servicio.'/asignar', ['id_tecnico' => $nuevo->id_tecnico]);

        $this->assertSame('en_curso', $this->servicio->fresh()->estado);
    }

    public function test_el_formulario_de_estado_muestra_los_tres_estados(): void
    {
        $this->actingAs($this->cuenta)->get('/mis-servicios/'.$this->servicio->id_servicio.'/estado')->assertOk()->assertSee('Pendiente')->assertSee('En curso')->assertSee('Completado');
    }
}
