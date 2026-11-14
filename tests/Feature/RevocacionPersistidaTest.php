<?php

namespace Tests\Feature;

use App\Services\TrabajoTecnico\RegistroTrabajoTecnico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RevocacionPersistidaTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    public function test_desactivar_la_cuenta_revoca_una_sesion_ya_iniciada_en_la_siguiente_peticion(): void
    {
        $usuario = $this->cuentaTecnico();
        $this->actingAs($usuario)->get('/panel')->assertOk();
        DB::table('usuario')->where('id_usuario', $usuario->id_usuario)->update(['activado' => false]);

        $this->getJson('/panel')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_el_cambio_de_rol_se_lee_de_la_base_y_no_de_una_instancia_antigua(): void
    {
        $usuario = $this->administrador();
        $this->actingAs($usuario);
        DB::table('usuario')->where('id_usuario', $usuario->id_usuario)->update(['rol' => 'tecnico', 'id_tecnico' => $this->tecnico()->id_tecnico]);
        $this->get('/cuentas/nueva')->assertForbidden();
    }

    public function test_el_listado_de_clientes_muestra_solo_quince_registros_por_pagina(): void
    {
        for ($i = 0; $i < 16; $i++) {
            $this->cliente(sprintf('Cliente %02d', $i));
        }
        $this->actingAs($this->administrador())->get('/clientes')
            ->assertOk()->assertSee('Cliente 00')->assertSee('Cliente 14')->assertDontSee('Cliente 15');
        $this->get('/clientes?page=2')->assertOk()->assertSee('Cliente 15');
    }

    public function test_el_historial_no_filtra_ordenes_completadas_de_otro_tecnico_del_mismo_equipo(): void
    {
        $usuario = $this->cuentaTecnico();
        $equipo = $this->equipo();
        $ajeno = $this->servicio($equipo, $this->tecnico('Otro'), 'completado', ['nombre' => 'Servicio reservado ajeno']);
        $this->trabajo($ajeno, 'reparacion', 'Detalle reservado ajeno');
        $actual = $this->servicio($equipo, $this->tecnicoDe($usuario));

        $this->actingAs($usuario)->get('/mis-servicios/'.$actual->id_servicio)
            ->assertOk()->assertDontSee('Servicio reservado ajeno')->assertDontSee('Detalle reservado ajeno');
    }

    public function test_la_transaccion_revalida_una_cuenta_aunque_la_instancia_este_desactualizada(): void
    {
        $usuario = $this->cuentaTecnico();
        $servicio = $this->servicio(tecnico: $this->tecnicoDe($usuario));
        DB::table('usuario')->where('id_usuario', $usuario->id_usuario)->update(['activado' => false]);

        try {
            app(RegistroTrabajoTecnico::class)->registrarDiagnostico($servicio->id_servicio, $usuario, ['resultado' => 'No autorizado']);
            $this->fail('Se guardó con una cuenta desactivada.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->assertDatabaseCount('diagnostico', 0);
    }

    public function test_el_listado_de_servicios_tiene_consultas_acotadas_y_quince_filas(): void
    {
        for ($i = 0; $i < 31; $i++) {
            $this->servicio(tecnico: $this->tecnico());
        }
        $this->actingAs($this->administrador());
        $consultas = 0;
        DB::listen(function () use (&$consultas): void {
            $consultas++;
        });

        $this->get('/servicios')->assertOk()->assertViewHas('servicios', fn ($paginas) => $paginas->count() === 15 && $paginas->total() === 31);
        $this->assertLessThanOrEqual(12, $consultas, 'El listado parece cargar relaciones una por una.');
    }

    public function test_los_indices_compuestos_de_los_listados_existen_en_postgresql(): void
    {
        $indices = array_column(DB::select("SELECT indexname FROM pg_indexes WHERE schemaname = 'public'"), 'indexname');
        foreach (['idx_cliente_nombre_id', 'idx_tecnico_nombre_id', 'idx_servicio_equipo_fecha_id', 'idx_diagnostico_servicio_id', 'idx_trabajo_servicio_id', 'idx_repuesto_servicio_id'] as $indice) {
            $this->assertContains($indice, $indices);
        }
    }
}
