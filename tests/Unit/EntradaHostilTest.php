<?php

namespace Tests\Unit;

use App\Http\Requests\ClientesEquipos\RegistrarEquipoRequest;
use App\Http\Requests\Consulta\ConsultarServicioRequest;
use App\Http\Requests\IniciarSesionRequest;
use App\Http\Requests\Servicios\SeleccionarClienteRequest;
use App\Http\Requests\TrabajoTecnico\RegistrarRepuestosRequest;
use App\Models\Usuario;
use App\Rules\TextoValido;
use App\Support\EntradaFormulario;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\PresenceVerifierInterface;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class EntradaHostilTest extends TestCase
{
    public function test_un_identificador_fuera_de_rango_no_llega_a_exists(): void
    {
        $presencia = $this->createMock(PresenceVerifierInterface::class);
        $presencia->expects($this->never())->method('getCount');
        $presencia->expects($this->never())->method('getMultiCount');
        $request = SeleccionarClienteRequest::create('/', 'GET', ['id_cliente' => '2147483648']);
        $validador = Validator::make($request->all(), $request->rules());
        $validador->setPresenceVerifier($presencia);
        $this->assertTrue($validador->fails());
        $this->assertNull(EntradaFormulario::identificador('2147483648'));
        $this->assertSame(2147483647, EntradaFormulario::identificador('2147483647'));
    }

    public function test_bytes_nulos_y_utf8_invalido_son_rechazados_y_nombres_legitimos_se_aceptan(): void
    {
        foreach (["Ana\0", "\xC3\x28"] as $texto) {
            $this->assertTrue(Validator::make(['texto' => $texto], ['texto' => ['string', new TextoValido]])->fails());
        }

        $this->assertTrue(Validator::make(['texto' => "María O'Connor"], ['texto' => ['string', new TextoValido]])->passes());
        foreach ([IniciarSesionRequest::class, ConsultarServicioRequest::class] as $clase) {
            $request = new $clase;
            $this->assertTrue(Validator::make(['nombre_usuario' => "a\0b", 'codigo_seguimiento' => "a\0b", 'password' => 'x'], $request->rules())->fails());
        }
    }

    public function test_fecha_de_anio_cero_no_pasa_la_validacion_de_postgresql(): void
    {
        $reglas = (new RegistrarEquipoRequest)->rules()['fecha_registro'];
        $this->assertTrue(Validator::make(['fecha_registro' => '0000-01-01'], ['fecha_registro' => $reglas])->fails());
        $this->assertTrue(Validator::make(['fecha_registro' => '2026-11-11'], ['fecha_registro' => $reglas])->passes());
    }

    public function test_lotes_excesivos_se_rechazan_antes_de_expandir_las_reglas(): void
    {
        $request = RegistrarRepuestosRequest::create('/', 'POST', ['repuestos' => array_fill(0, 101, ['nombre' => 'RAM', 'cantidad' => 1])]);
        $this->expectException(ValidationException::class);
        $request->validationData();
    }

    public function test_no_se_pueden_asignar_masivamente_privilegios_ni_secretos(): void
    {
        $usuario = new Usuario(['nombre_usuario' => 'normal', 'rol' => 'administrador', 'activado' => true, 'contrasena_hash' => 'inyectado', 'codigo_activacion' => 'inyectado']);
        $this->assertSame(['nombre_usuario' => 'normal'], $usuario->getAttributes());
    }

    public function test_una_pagina_extrema_no_produce_un_offset_fuera_de_rango(): void
    {
        request()->query->set('page', '999999999999999999');
        $this->expectException(NotFoundHttpException::class);
        Paginator::resolveCurrentPage();
    }
}
