<?php

namespace Tests\Unit;

use App\Http\Middleware\CabecerasSeguras;
use App\Http\Middleware\VerificarRol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MiddlewareSeguridadTest extends TestCase
{
    private function respuesta(): Response
    {
        return (new CabecerasSeguras)->handle(Request::create('/'), fn () => new Response('ok'));
    }

    private function solicitudDe(?string $rol): Request
    {
        $solicitud = Request::create('/servicios');
        $solicitud->setUserResolver(fn () => $rol ? new Usuario(['nombre_usuario' => 'ficticio', 'rol' => $rol]) : null);

        return $solicitud;
    }

    public function test_las_respuestas_prohiben_mostrarse_dentro_de_marcos(): void
    {
        $this->assertSame('DENY', $this->respuesta()->headers->get('X-Frame-Options'));
    }

    public function test_las_respuestas_incluyen_politica_de_contenido(): void
    {
        $this->assertStringContainsString("script-src 'self'", $this->respuesta()->headers->get('Content-Security-Policy'));
    }

    public function test_las_respuestas_impiden_adivinar_el_tipo_de_contenido(): void
    {
        $this->assertSame('nosniff', $this->respuesta()->headers->get('X-Content-Type-Options'));
    }

    public function test_las_paginas_dinamicas_no_se_guardan_en_cache(): void
    {
        $this->assertStringContainsString('no-store', $this->respuesta()->headers->get('Cache-Control'));
    }

    public function test_un_tecnico_es_bloqueado_en_una_ruta_de_administrador(): void
    {
        try {
            (new VerificarRol)->handle($this->solicitudDe('tecnico'), fn () => new Response('ok'), 'administrador');
            $this->fail('El técnico pasó el filtro de administrador.');
        } catch (HttpException $excepcion) {
            $this->assertSame(403, $excepcion->getStatusCode());
        }
    }

    public function test_un_administrador_es_bloqueado_en_una_ruta_de_tecnico(): void
    {
        $this->expectException(HttpException::class);

        (new VerificarRol)->handle($this->solicitudDe('administrador'), fn () => new Response('ok'), 'tecnico');
    }

    public function test_una_solicitud_sin_usuario_es_bloqueada(): void
    {
        $this->expectException(HttpException::class);

        (new VerificarRol)->handle($this->solicitudDe(null), fn () => new Response('ok'), 'administrador');
    }

    public function test_el_rol_correcto_pasa_el_filtro(): void
    {
        $respuesta = (new VerificarRol)->handle($this->solicitudDe('administrador'), fn () => new Response('ok'), 'administrador');

        $this->assertSame('ok', $respuesta->getContent());
    }
}
