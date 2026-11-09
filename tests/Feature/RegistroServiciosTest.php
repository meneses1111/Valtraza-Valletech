<?php

namespace Tests\Feature;

use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RegistroServiciosTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function datos(array $cambios = []): array
    {
        $equipo = $this->equipo();

        return [
            'id_cliente' => $equipo->id_cliente,
            'id_equipo' => $equipo->id_equipo,
            'fecha' => '2026-11-09',
            'nombre' => 'Reparación de teclado',
            'descripcion_problema' => 'Algunas teclas no responden',
            ...$cambios,
        ];
    }

    private function registrar(array $datos)
    {
        return $this->actingAs($this->administrador())->from('/servicios/nuevo')->post('/servicios', $datos);
    }

    public function test_el_administrador_registra_un_servicio_para_el_equipo_de_un_cliente(): void
    {
        $this->registrar($this->datos())->assertSessionHasNoErrors();

        $this->assertDatabaseHas('servicio', ['nombre' => 'Reparación de teclado', 'descripcion_problema' => 'Algunas teclas no responden']);
    }

    public function test_el_servicio_nuevo_queda_pendiente_y_sin_tecnico(): void
    {
        $this->registrar($this->datos());

        $servicio = Servicio::query()->first();
        $this->assertSame('pendiente', $servicio->estado);
        $this->assertNull($servicio->id_tecnico);
    }

    public function test_el_registro_lleva_a_la_confirmacion_con_los_dos_codigos(): void
    {
        $respuesta = $this->registrar($this->datos());
        $servicio = Servicio::query()->first();

        $respuesta->assertRedirect('/servicios/'.$servicio->id_servicio.'/registrado');
        $this->get('/servicios/'.$servicio->id_servicio.'/registrado')->assertOk()->assertSee($servicio->codigo_servicio)->assertSee($servicio->codigo_seguimiento);
    }

    public function test_el_servicio_recibe_un_codigo_de_servicio_automatico(): void
    {
        $this->registrar($this->datos());

        $this->assertMatchesRegularExpression('/^SRV-\d{4,}$/', Servicio::query()->first()->codigo_servicio);
    }

    public function test_el_codigo_de_seguimiento_es_dificil_de_adivinar(): void
    {
        $this->registrar($this->datos());

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', Servicio::query()->first()->codigo_seguimiento);
    }

    public function test_dos_servicios_tienen_codigos_distintos(): void
    {
        $this->registrar($this->datos());
        $this->registrar($this->datos());

        $this->assertSame(2, Servicio::query()->distinct()->count('codigo_servicio'));
        $this->assertSame(2, Servicio::query()->distinct()->count('codigo_seguimiento'));
    }

    public function test_los_codigos_enviados_en_el_formulario_son_ignorados(): void
    {
        $this->registrar($this->datos(['codigo_seguimiento' => 'abc', 'codigo_servicio' => 'SRV-HACK', 'estado' => 'completado', 'id_tecnico' => $this->tecnico()->id_tecnico]));

        $servicio = Servicio::query()->first();
        $this->assertNotSame('abc', $servicio->codigo_seguimiento);
        $this->assertNotSame('SRV-HACK', $servicio->codigo_servicio);
        $this->assertSame('pendiente', $servicio->estado);
        $this->assertNull($servicio->id_tecnico);
    }

    public function test_el_nombre_del_servicio_es_obligatorio(): void
    {
        $this->registrar($this->datos(['nombre' => '']))->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('servicio', 0);
    }

    public function test_la_descripcion_del_problema_es_obligatoria(): void
    {
        $this->registrar($this->datos(['descripcion_problema' => '']))->assertSessionHasErrors('descripcion_problema');

        $this->assertDatabaseCount('servicio', 0);
    }

    public function test_la_fecha_del_servicio_es_obligatoria(): void
    {
        $this->registrar($this->datos(['fecha' => '']))->assertSessionHasErrors('fecha');
    }

    public function test_una_fecha_inexistente_es_rechazada(): void
    {
        $this->registrar($this->datos(['fecha' => '2026-02-30']))->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('servicio', 0);
    }

    public function test_el_cliente_es_obligatorio(): void
    {
        $this->registrar($this->datos(['id_cliente' => '']))->assertSessionHasErrors('id_cliente');
    }

    public function test_el_computador_es_obligatorio(): void
    {
        $this->registrar($this->datos(['id_equipo' => '']))->assertSessionHasErrors('id_equipo');
    }

    public function test_un_computador_de_otro_cliente_es_rechazado(): void
    {
        $otroCliente = $this->cliente('Otra Persona');

        $this->registrar($this->datos(['id_cliente' => $otroCliente->id_cliente]))->assertSessionHasErrors('id_equipo');

        $this->assertDatabaseCount('servicio', 0);
    }

    public function test_un_computador_inexistente_es_rechazado(): void
    {
        $this->registrar($this->datos(['id_equipo' => 999999]))->assertSessionHasErrors('id_equipo');
    }

    public function test_un_nuevo_servicio_conserva_los_anteriores_del_mismo_computador(): void
    {
        $equipo = $this->equipo();
        $anterior = $this->servicio($equipo, datos: ['nombre' => 'Servicio anterior']);

        $this->registrar(['id_cliente' => $equipo->id_cliente, 'id_equipo' => $equipo->id_equipo, 'fecha' => '2026-11-09', 'nombre' => 'Servicio nuevo', 'descripcion_problema' => 'Pantalla rota']);

        $this->assertSame(2, Servicio::query()->where('id_equipo', $equipo->id_equipo)->count());
        $this->assertSame('Servicio anterior', $anterior->fresh()->nombre);
    }

    public function test_el_formulario_permite_elegir_entre_los_computadores_del_cliente(): void
    {
        $cliente = $this->cliente();
        $this->equipo($cliente, 'Portátil propio');
        $this->equipo(null, 'Equipo ajeno');

        $this->actingAs($this->administrador())->get('/servicios/nuevo?id_cliente='.$cliente->id_cliente)
            ->assertOk()->assertSee('Portátil propio')->assertDontSee('Equipo ajeno');
    }

    public function test_el_formulario_ofrece_registrar_cliente_o_equipo_si_no_existen(): void
    {
        $this->actingAs($this->administrador())->get('/servicios/nuevo')->assertOk()->assertSee('/clientes', false);
    }
}
