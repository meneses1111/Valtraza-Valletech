<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class BlindajeHttpTest extends TestCase
{
    private function usuario(string $rol = 'administrador', bool $activo = true): Usuario
    {
        return (new Usuario)->forceFill(['nombre_usuario' => 'fixture', 'rol' => $rol, 'activado' => $activo]);
    }

    public function test_identificadores_extremos_devuelven_404_sin_consultar_postgresql(): void
    {
        $this->actingAs($this->usuario());

        foreach (['0', '2147483648', str_repeat('9', 40)] as $id) {
            $this->get('/clientes/'.$id)->assertNotFound()->assertHeader('X-Content-Type-Options', 'nosniff');
        }
    }

    public function test_una_cuenta_inactiva_pierde_la_sesion(): void
    {
        $this->actingAs($this->usuario('tecnico', false));
        $this->getJson('/panel')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_el_intento_21_de_login_se_bloquea_por_ip_aunque_cambie_el_usuario(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.51']);

        for ($i = 0; $i < 20; $i++) {
            $this->withHeader('X-Forwarded-For', '203.0.113.'.($i + 1))
                ->postJson('/ingresar', ['nombre_usuario' => 'usuario'.$i, 'password' => []])->assertUnprocessable();
        }

        $this->postJson('/ingresar', ['nombre_usuario' => 'otro', 'password' => []])
            ->assertTooManyRequests()->assertHeader('Retry-After')->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_la_consulta_publica_numero_once_se_bloquea_por_ip(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning')->with('seguridad.limite_excedido', $this->callback(fn (array $datos) => array_key_exists('ip', $datos) && array_key_exists('user_id', $datos)));
        Log::swap($logger);
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.52']);

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/consultar', ['codigo_seguimiento' => []])->assertUnprocessable();
        }

        $this->postJson('/consultar', ['codigo_seguimiento' => []])->assertTooManyRequests();
    }

    public function test_403_se_audita_sin_incluir_el_cuerpo_de_la_peticion(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning')->with('seguridad.acceso_denegado', $this->callback(function (array $datos): bool {
            return array_key_exists('ip', $datos) && array_key_exists('user_id', $datos)
                && ! str_contains(json_encode($datos), 'secreto-no-registrar');
        }));
        Log::swap($logger);
        $this->actingAs($this->usuario('tecnico'));
        $this->get('/cuentas/nueva?password=secreto-no-registrar')->assertForbidden();
    }

    public function test_un_fallo_del_log_no_convierte_un_acceso_denegado_en_500(): void
    {
        $logger = $this->createStub(LoggerInterface::class);
        $logger->method('warning')->willThrowException(new \RuntimeException('logger no disponible'));
        Log::swap($logger);
        $this->actingAs($this->usuario('tecnico'));
        $this->getJson('/cuentas/nueva')->assertForbidden();
    }

    public function test_las_excepciones_no_revelan_sql_ni_trazas_con_debug_desactivado(): void
    {
        config(['app.debug' => false]);
        Route::get('/_prueba/error-sql', function (): void {
            throw new QueryException('pgsql', 'SELECT secreto_interno FROM usuario', [], new \PDOException('detalle_privado', 23505));
        });

        $respuesta = $this->getJson('/_prueba/error-sql')->assertStatus(500);
        $respuesta->assertDontSee('secreto_interno')->assertDontSee('detalle_privado')->assertDontSee('trace');
    }

    public function test_postgresql_no_disponible_responde_503_sin_detalles_internos(): void
    {
        config(['app.debug' => false]);
        Route::get('/_prueba/postgresql-no-disponible', function (): void {
            $causa = new \PDOException('credencial_privada');
            $causa->errorInfo = ['08006', 7, 'credencial_privada'];
            throw new QueryException('pgsql', 'SELECT dato_privado', [], $causa);
        });

        $this->getJson('/_prueba/postgresql-no-disponible')->assertStatus(503)
            ->assertHeader('Retry-After', '30')->assertDontSee('credencial_privada')->assertDontSee('dato_privado');
    }
}
