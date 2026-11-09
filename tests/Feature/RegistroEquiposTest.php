<?php

namespace Tests\Feature;

use App\Models\Equipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RegistroEquiposTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function registrar(int $idCliente, array $datos)
    {
        return $this->actingAs($this->administrador())->from('/clientes/'.$idCliente)->post('/clientes/'.$idCliente.'/equipos', $datos);
    }

    public function test_el_administrador_registra_un_equipo_con_cliente_y_fecha(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09'])->assertRedirect('/clientes/'.$cliente->id_cliente)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipo', ['id_cliente' => $cliente->id_cliente, 'fecha_registro' => '2026-11-09']);
    }

    public function test_el_equipo_recibe_un_codigo_unico_automatico(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09']);

        $this->assertMatchesRegularExpression('/^EQ-\d{4,}$/', Equipo::query()->first()->codigo_equipo);
    }

    public function test_los_equipos_reciben_codigos_distintos(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09']);
        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09']);

        $this->assertSame(2, Equipo::query()->distinct()->count('codigo_equipo'));
    }

    public function test_el_codigo_enviado_en_el_formulario_es_ignorado(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09', 'codigo_equipo' => 'EQ-HACK']);

        $this->assertDatabaseMissing('equipo', ['codigo_equipo' => 'EQ-HACK']);
    }

    public function test_el_modelo_y_los_datos_tecnicos_son_opcionales(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09', 'modelo' => 'Lenovo ficticio', 'otros_datos_tecnicos' => '8 GB RAM']);

        $this->assertDatabaseHas('equipo', ['modelo' => 'Lenovo ficticio', 'otros_datos_tecnicos' => '8 GB RAM']);
    }

    public function test_la_fecha_es_obligatoria(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => ''])->assertSessionHasErrors('fecha_registro');

        $this->assertDatabaseCount('equipo', 0);
    }

    public function test_una_fecha_inexistente_es_rechazada(): void
    {
        $cliente = $this->cliente();

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-02-31'])->assertSessionHasErrors('fecha_registro');

        $this->assertDatabaseCount('equipo', 0);
    }

    public function test_un_cliente_inexistente_no_recibe_equipos(): void
    {
        $this->registrar(999999, ['fecha_registro' => '2026-11-09'])->assertNotFound();

        $this->assertDatabaseCount('equipo', 0);
    }

    public function test_el_cliente_enviado_en_el_formulario_no_cambia_el_de_la_ruta(): void
    {
        $cliente = $this->cliente();
        $otro = $this->cliente('Otro Cliente');

        $this->registrar($cliente->id_cliente, ['fecha_registro' => '2026-11-09', 'id_cliente' => $otro->id_cliente]);

        $this->assertSame($cliente->id_cliente, Equipo::query()->first()->id_cliente);
    }

    public function test_un_cliente_puede_tener_varios_equipos(): void
    {
        $cliente = $this->cliente();
        $this->equipo($cliente, 'Portátil A');
        $this->equipo($cliente, 'Torre B');

        $this->actingAs($this->administrador())->get('/clientes/'.$cliente->id_cliente)->assertOk()->assertSee('Portátil A')->assertSee('Torre B');
    }

    public function test_la_ficha_muestra_el_historial_de_servicios_de_cada_equipo(): void
    {
        $equipo = $this->equipo();
        $this->servicio($equipo, datos: ['nombre' => 'Cambio de pantalla']);
        $this->servicio($equipo, datos: ['nombre' => 'Formateo ficticio']);

        $this->actingAs($this->administrador())->get('/clientes/'.$equipo->id_cliente)->assertSee('Cambio de pantalla')->assertSee('Formateo ficticio');
    }
}
