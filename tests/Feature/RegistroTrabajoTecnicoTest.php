<?php

namespace Tests\Feature;

use App\Models\Servicio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\DatosDePrueba;
use Tests\TestCase;

class RegistroTrabajoTecnicoTest extends TestCase
{
    use DatosDePrueba, RefreshDatabase;

    private Usuario $cuenta;

    private Servicio $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cuenta = $this->cuentaTecnico();
        $this->servicio = $this->servicio(tecnico: $this->tecnicoDe($this->cuenta), estado: 'en_curso');
    }

    private function enviar(string $seccion, array $datos, ?Usuario $cuenta = null)
    {
        $ruta = '/mis-servicios/'.$this->servicio->id_servicio.'/'.$seccion;

        return $this->actingAs($cuenta ?? $this->cuenta)->from($ruta)->post($ruta, $datos);
    }

    public function test_el_tecnico_registra_un_diagnostico_con_fallas_y_pruebas(): void
    {
        $this->enviar('diagnosticos', ['resultado' => 'Fuente sin voltaje', 'pruebas_realizadas' => 'Multímetro', 'observaciones' => 'Cliente avisado'])
            ->assertRedirect('/mis-servicios/'.$this->servicio->id_servicio);

        $this->assertDatabaseHas('diagnostico', ['id_servicio' => $this->servicio->id_servicio, 'resultado' => 'Fuente sin voltaje', 'pruebas_realizadas' => 'Multímetro', 'observaciones' => 'Cliente avisado']);
    }

    public function test_el_diagnostico_sin_resultado_no_se_guarda(): void
    {
        $this->enviar('diagnosticos', ['resultado' => '', 'pruebas_realizadas' => 'Multímetro'])->assertSessionHasErrors('resultado');

        $this->assertDatabaseCount('diagnostico', 0);
    }

    public function test_el_diagnostico_puede_indicar_que_no_hay_fallas(): void
    {
        $this->enviar('diagnosticos', ['sin_fallas' => '1'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('diagnostico', ['id_servicio' => $this->servicio->id_servicio, 'resultado' => 'No se encontraron fallas', 'fallas_identificadas' => null]);
    }

    public function test_un_segundo_diagnostico_no_reemplaza_el_primero(): void
    {
        $this->enviar('diagnosticos', ['resultado' => 'Primera revisión']);
        $this->enviar('diagnosticos', ['resultado' => 'Segunda revisión']);

        $this->assertDatabaseCount('diagnostico', 2);
    }

    public function test_el_tecnico_registra_un_mantenimiento(): void
    {
        $this->enviar('trabajos', ['tipo' => 'mantenimiento', 'descripcion' => 'Limpieza interna', 'observaciones' => 'Polvo excesivo'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('trabajo', ['id_servicio' => $this->servicio->id_servicio, 'tipo' => 'mantenimiento', 'descripcion' => 'Limpieza interna']);
    }

    public function test_el_tecnico_registra_una_reparacion(): void
    {
        $this->enviar('trabajos', ['tipo' => 'reparacion', 'descripcion' => 'Cambio de bisagra'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('trabajo', ['tipo' => 'reparacion', 'descripcion' => 'Cambio de bisagra']);
    }

    public function test_varias_actividades_se_acumulan_sin_reemplazarse(): void
    {
        $this->enviar('trabajos', ['tipo' => 'mantenimiento', 'descripcion' => 'Limpieza interna']);
        $this->enviar('trabajos', ['tipo' => 'reparacion', 'descripcion' => 'Cambio de bisagra']);

        $this->assertDatabaseCount('trabajo', 2);
    }

    public function test_la_actividad_sin_descripcion_no_se_guarda(): void
    {
        $this->enviar('trabajos', ['tipo' => 'mantenimiento', 'descripcion' => ''])->assertSessionHasErrors('descripcion');

        $this->assertDatabaseCount('trabajo', 0);
    }

    public function test_la_actividad_con_tipo_ajeno_no_se_guarda(): void
    {
        $this->enviar('trabajos', ['tipo' => 'instalacion', 'descripcion' => 'Algo'])->assertSessionHasErrors('tipo');

        $this->assertDatabaseCount('trabajo', 0);
    }

    public function test_el_tecnico_registra_varios_repuestos_con_cantidad(): void
    {
        $this->enviar('repuestos', ['repuestos' => [
            ['nombre' => 'Disco SSD 480 GB', 'cantidad' => 1, 'observacion' => 'Nuevo'],
            ['nombre' => 'Tornillos M2', 'cantidad' => 4],
        ]])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repuesto', ['nombre' => 'Disco SSD 480 GB', 'cantidad' => 1, 'observacion' => 'Nuevo']);
        $this->assertDatabaseHas('repuesto', ['nombre' => 'Tornillos M2', 'cantidad' => 4, 'observacion' => null]);
    }

    public static function repuestosInvalidos(): array
    {
        return [
            'cantidad cero' => [['nombre' => 'Disco', 'cantidad' => 0]],
            'cantidad negativa' => [['nombre' => 'Disco', 'cantidad' => -2]],
            'cantidad decimal' => [['nombre' => 'Disco', 'cantidad' => '1.5']],
            'nombre vacio' => [['nombre' => '', 'cantidad' => 1]],
        ];
    }

    #[DataProvider('repuestosInvalidos')]
    public function test_un_repuesto_invalido_no_se_guarda(array $repuesto): void
    {
        $this->enviar('repuestos', ['repuestos' => [$repuesto]])->assertSessionHasErrors();

        $this->assertDatabaseCount('repuesto', 0);
    }

    public function test_una_fila_invalida_impide_guardar_todas_las_filas(): void
    {
        $this->enviar('repuestos', ['repuestos' => [['nombre' => 'Disco', 'cantidad' => 1], ['nombre' => 'Cable', 'cantidad' => 0]]])->assertSessionHasErrors();

        $this->assertDatabaseCount('repuesto', 0);
    }

    public function test_el_avance_queda_visible_en_el_detalle_del_tecnico(): void
    {
        $this->enviar('diagnosticos', ['resultado' => 'Pantalla con píxeles muertos']);

        $this->get('/mis-servicios/'.$this->servicio->id_servicio)->assertSee('Pantalla con píxeles muertos')->assertSee('Diagnóstico registrado.');
    }

    public static function secciones(): array
    {
        return [
            'diagnostico' => ['diagnosticos', ['resultado' => 'Intento ajeno']],
            'actividad' => ['trabajos', ['tipo' => 'mantenimiento', 'descripcion' => 'Intento ajeno']],
            'repuestos' => ['repuestos', ['repuestos' => [['nombre' => 'Intento ajeno', 'cantidad' => 1]]]],
            'estado' => ['estado', ['estado' => 'en_curso']],
        ];
    }

    #[DataProvider('secciones')]
    public function test_otro_tecnico_no_puede_registrar_en_un_servicio_ajeno(string $seccion, array $datos): void
    {
        $this->enviar($seccion, $datos, $this->cuentaTecnico())->assertForbidden();

        $this->assertDatabaseCount('diagnostico', 0);
        $this->assertDatabaseCount('trabajo', 0);
        $this->assertDatabaseCount('repuesto', 0);
    }

    #[DataProvider('secciones')]
    public function test_otro_tecnico_no_puede_abrir_los_formularios_de_un_servicio_ajeno(string $seccion): void
    {
        $this->actingAs($this->cuentaTecnico())->get('/mis-servicios/'.$this->servicio->id_servicio.'/'.$seccion)->assertForbidden();
    }

    #[DataProvider('secciones')]
    public function test_el_administrador_no_modifica_el_trabajo_del_tecnico(string $seccion, array $datos): void
    {
        $this->enviar($seccion, $datos, $this->administrador())->assertForbidden();

        $this->assertDatabaseCount('diagnostico', 0);
    }

    public function test_el_registro_en_un_servicio_reasignado_es_rechazado_para_el_tecnico_anterior(): void
    {
        $this->servicio->id_tecnico = $this->tecnico('Nuevo encargado')->id_tecnico;
        $this->servicio->save();

        $this->enviar('diagnosticos', ['resultado' => 'Registro tardío'])->assertForbidden();

        $this->assertDatabaseCount('diagnostico', 0);
    }
}
