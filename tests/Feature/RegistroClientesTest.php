<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RegistroClientesTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private function registrar(array $datos)
    {
        return $this->actingAs($this->administrador())->from('/clientes')->post('/clientes', $datos);
    }

    public function test_el_administrador_registra_un_cliente_con_nombre_e_identificacion(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cliente', ['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200', 'correo' => null]);
    }

    public function test_al_registrar_un_cliente_se_abre_su_ficha(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200'])
            ->assertRedirect('/clientes/'.Cliente::query()->first()->id_cliente);
    }

    public function test_el_correo_del_cliente_es_opcional_y_se_guarda_si_se_envia(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200', 'correo' => 'mario@ejemplo.test']);

        $this->assertDatabaseHas('cliente', ['numero_identificacion' => '1100200', 'correo' => 'mario@ejemplo.test']);
    }

    public function test_el_nombre_del_cliente_es_obligatorio(): void
    {
        $this->registrar(['nombre' => '', 'numero_identificacion' => '1100200'])->assertSessionHasErrors('nombre');

        $this->assertDatabaseCount('cliente', 0);
    }

    public function test_la_identificacion_del_cliente_es_obligatoria(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => ''])->assertSessionHasErrors('numero_identificacion');

        $this->assertDatabaseCount('cliente', 0);
    }

    public function test_un_correo_invalido_es_rechazado(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200', 'correo' => 'mario@'])->assertSessionHasErrors('correo');
    }

    public function test_una_identificacion_repetida_no_duplica_el_cliente(): void
    {
        $this->cliente('Mario Ficticio', '1100200');

        $this->registrar(['nombre' => 'Otro Nombre', 'numero_identificacion' => '1100200'])->assertSessionHasErrors('numero_identificacion');

        $this->assertSame(1, Cliente::query()->where('numero_identificacion', '1100200')->count());
    }

    public function test_una_identificacion_repetida_con_espacios_no_duplica_el_cliente(): void
    {
        $this->cliente('Mario Ficticio', '1100200');

        $this->registrar(['nombre' => 'Otro Nombre', 'numero_identificacion' => ' 1100200 ']);

        $this->assertSame(1, Cliente::query()->where('numero_identificacion', 'like', '%1100200%')->count());
    }

    public function test_el_aviso_de_cliente_repetido_permite_consultar_su_ficha(): void
    {
        $cliente = $this->cliente('Mario Ficticio', '1100200');

        $this->followingRedirects()->actingAs($this->administrador())->from('/clientes')
            ->post('/clientes', ['nombre' => 'Otro Nombre', 'numero_identificacion' => '1100200'])
            ->assertSee('Este cliente ya existe')->assertSee('/clientes/'.$cliente->id_cliente, false);
    }

    public function test_registrar_un_cliente_no_crea_una_cuenta_de_acceso(): void
    {
        $this->registrar(['nombre' => 'Mario Ficticio', 'numero_identificacion' => '1100200']);

        $this->assertSame(1, Usuario::query()->count());
    }

    public function test_la_busqueda_por_identificacion_encuentra_al_cliente(): void
    {
        $this->cliente('Mario Ficticio', '1100200');
        $this->cliente('Ana Ficticia', '3300400');

        $this->actingAs($this->administrador())->get('/clientes?numero_identificacion=1100200')
            ->assertOk()->assertSee('Mario Ficticio')->assertDontSee('Ana Ficticia');
    }

    public function test_la_busqueda_sin_coincidencias_muestra_un_aviso(): void
    {
        $this->actingAs($this->administrador())->get('/clientes?numero_identificacion=000')->assertOk()->assertSee('No se encontraron clientes.');
    }

    public function test_una_ficha_inexistente_responde_no_encontrado(): void
    {
        $this->actingAs($this->administrador())->get('/clientes/999999')->assertNotFound();
    }
}
