<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class ControlAccesoRolesTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public static function rutasDeAdministrador(): array
    {
        return [
            'lista de servicios' => ['get', '/servicios'],
            'nuevo servicio' => ['get', '/servicios/nuevo'],
            'guardar servicio' => ['post', '/servicios'],
            'detalle de servicio' => ['get', '/servicios/{servicio}'],
            'confirmacion de servicio' => ['get', '/servicios/{servicio}/registrado'],
            'pantalla de asignacion' => ['get', '/servicios/{servicio}/asignar'],
            'guardar asignacion' => ['post', '/servicios/{servicio}/asignar'],
            'clientes' => ['get', '/clientes'],
            'guardar cliente' => ['post', '/clientes'],
            'ficha de cliente' => ['get', '/clientes/{cliente}'],
            'guardar equipo' => ['post', '/clientes/{cliente}/equipos'],
            'tecnicos' => ['get', '/tecnicos'],
            'guardar tecnico' => ['post', '/tecnicos'],
            'cuentas' => ['get', '/cuentas/nueva'],
            'guardar cuenta' => ['post', '/cuentas'],
        ];
    }

    public static function rutasDeTecnico(): array
    {
        return [
            'mis servicios' => ['get', '/mis-servicios'],
            'detalle' => ['get', '/mis-servicios/{servicio}'],
            'formulario diagnostico' => ['get', '/mis-servicios/{servicio}/diagnosticos'],
            'guardar diagnostico' => ['post', '/mis-servicios/{servicio}/diagnosticos'],
            'formulario actividad' => ['get', '/mis-servicios/{servicio}/trabajos'],
            'guardar actividad' => ['post', '/mis-servicios/{servicio}/trabajos'],
            'formulario repuestos' => ['get', '/mis-servicios/{servicio}/repuestos'],
            'guardar repuestos' => ['post', '/mis-servicios/{servicio}/repuestos'],
            'formulario estado' => ['get', '/mis-servicios/{servicio}/estado'],
            'guardar estado' => ['post', '/mis-servicios/{servicio}/estado'],
        ];
    }

    private function ruta(string $plantilla): string
    {
        $servicio = $this->servicio();

        return str_replace(['{servicio}', '{cliente}'], [$servicio->id_servicio, $servicio->equipo->id_cliente], $plantilla);
    }

    #[DataProvider('rutasDeAdministrador')]
    public function test_un_visitante_es_enviado_a_ingresar_desde_rutas_de_administrador(string $metodo, string $ruta): void
    {
        $this->{$metodo}($this->ruta($ruta))->assertRedirect('/ingresar');
    }

    #[DataProvider('rutasDeTecnico')]
    public function test_un_visitante_es_enviado_a_ingresar_desde_rutas_de_tecnico(string $metodo, string $ruta): void
    {
        $this->{$metodo}($this->ruta($ruta))->assertRedirect('/ingresar');
    }

    #[DataProvider('rutasDeAdministrador')]
    public function test_un_tecnico_es_bloqueado_en_rutas_de_administrador(string $metodo, string $ruta): void
    {
        $this->actingAs($this->cuentaTecnico())->{$metodo}($this->ruta($ruta))->assertForbidden();
    }

    #[DataProvider('rutasDeTecnico')]
    public function test_un_administrador_es_bloqueado_en_rutas_de_tecnico(string $metodo, string $ruta): void
    {
        $this->actingAs($this->administrador())->{$metodo}($this->ruta($ruta))->assertForbidden();
    }

    public function test_el_bloqueo_muestra_el_aviso_de_acceso_no_autorizado(): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/clientes')->assertForbidden()->assertSee('Acceso no autorizado')->assertSee('No tienes permiso');
    }

    public function test_un_bloqueo_de_tecnico_no_registra_datos(): void
    {
        $this->actingAs($this->cuentaTecnico())->post('/clientes', ['nombre' => 'Intruso', 'numero_identificacion' => '666']);

        $this->assertDatabaseMissing('cliente', ['numero_identificacion' => '666']);
    }

    public function test_la_consulta_publica_funciona_sin_iniciar_sesion(): void
    {
        $servicio = $this->servicio();

        $this->post('/consultar', ['codigo_seguimiento' => $servicio->codigo_seguimiento])->assertOk();
    }

    public function test_la_consulta_publica_tambien_funciona_con_sesion_iniciada(): void
    {
        $servicio = $this->servicio();

        $this->actingAs($this->cuentaTecnico())->post('/consultar', ['codigo_seguimiento' => $servicio->codigo_seguimiento])->assertOk();
    }

    public function test_un_identificador_no_numerico_en_la_ruta_no_existe(): void
    {
        $this->actingAs($this->administrador())->get('/servicios/abc')->assertNotFound();
    }

    public function test_un_identificador_con_inyeccion_en_la_ruta_no_existe(): void
    {
        $this->actingAs($this->administrador())->get('/servicios/1%20OR%201=1')->assertNotFound();
    }
}
