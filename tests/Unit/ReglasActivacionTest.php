<?php

namespace Tests\Unit;

use App\Http\Requests\ActivarCuentaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReglasActivacionTest extends TestCase
{
    private function falla(array $datos): bool
    {
        return Validator::make($datos, (new ActivarCuentaRequest)->rules())->fails();
    }

    private function datos(string $contrasena, ?string $confirmacion = null): array
    {
        return ['codigo_activacion' => 'codigo-ficticio', 'password' => $contrasena, 'password_confirmation' => $confirmacion ?? $contrasena];
    }

    public function test_una_contrasena_de_siete_caracteres_es_rechazada(): void
    {
        $this->assertTrue($this->falla($this->datos('abcdefg')));
    }

    public function test_una_contrasena_de_ocho_caracteres_es_aceptada(): void
    {
        $this->assertFalse($this->falla($this->datos('abcdefgh')));
    }

    public function test_la_confirmacion_debe_coincidir(): void
    {
        $this->assertTrue($this->falla($this->datos('abcdefgh', 'abcdefgx')));
    }

    public function test_una_contrasena_de_72_bytes_es_aceptada(): void
    {
        $this->assertFalse($this->falla($this->datos(str_repeat('a', 72))));
    }

    public function test_una_contrasena_de_73_bytes_es_rechazada(): void
    {
        $this->assertTrue($this->falla($this->datos(str_repeat('a', 73))));
    }

    public function test_una_contrasena_con_tildes_se_mide_en_bytes(): void
    {
        $this->assertTrue($this->falla($this->datos(str_repeat('ñ', 37))));
    }

    public function test_una_contrasena_con_caracter_nulo_es_rechazada(): void
    {
        $this->assertTrue($this->falla($this->datos("abcdefgh\0")));
    }

    public function test_el_codigo_de_activacion_es_obligatorio(): void
    {
        $this->assertTrue($this->falla(['password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh']));
    }

    public function test_la_contrasena_es_obligatoria(): void
    {
        $this->assertTrue($this->falla(['codigo_activacion' => 'codigo-ficticio']));
    }
}
