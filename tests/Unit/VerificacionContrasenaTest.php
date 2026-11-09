<?php

namespace Tests\Unit;

use App\Models\Usuario;
use App\Services\Cuentas\Acceso;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificacionContrasenaTest extends TestCase
{
    private function usuario(string $contrasena, bool $activado = true): Usuario
    {
        $usuario = new Usuario(['nombre_usuario' => 'ficticio', 'rol' => 'administrador']);
        $usuario->activado = $activado;
        $usuario->contrasena_hash = Hash::make($contrasena);

        return $usuario;
    }

    public function test_una_cuenta_activa_con_la_contrasena_correcta_es_aceptada(): void
    {
        $this->assertTrue((new Acceso)->verificar($this->usuario('Clave-ficticia-1'), 'Clave-ficticia-1'));
    }

    public function test_una_contrasena_incorrecta_es_rechazada(): void
    {
        $this->assertFalse((new Acceso)->verificar($this->usuario('Clave-ficticia-1'), 'Clave-ficticia-2'));
    }

    public function test_una_cuenta_sin_activar_es_rechazada_aunque_la_contrasena_coincida(): void
    {
        $this->assertFalse((new Acceso)->verificar($this->usuario('Clave-ficticia-1', false), 'Clave-ficticia-1'));
    }

    public function test_una_contrasena_de_mas_de_72_bytes_es_rechazada(): void
    {
        $base = str_repeat('a', 72);

        $this->assertFalse((new Acceso)->verificar($this->usuario($base), $base.'extra'));
    }

    public function test_una_contrasena_con_caracter_nulo_es_rechazada(): void
    {
        $this->assertFalse((new Acceso)->verificar($this->usuario('Clave-ficticia-1'), "Clave-ficticia-1\0"));
    }

    public function test_una_cuenta_sin_contrasena_guardada_es_rechazada(): void
    {
        $usuario = new Usuario(['nombre_usuario' => 'ficticio', 'rol' => 'administrador']);
        $usuario->activado = true;

        $this->assertFalse((new Acceso)->verificar($usuario, ''));
    }

    public function test_la_contrasena_no_se_guarda_como_texto_visible(): void
    {
        $this->assertStringNotContainsString('Clave-ficticia-1', $this->usuario('Clave-ficticia-1')->contrasena_hash);
    }

    public function test_cada_contrasena_recibe_una_sal_diferente(): void
    {
        $this->assertNotSame($this->usuario('Clave-ficticia-1')->contrasena_hash, $this->usuario('Clave-ficticia-1')->contrasena_hash);
    }

    public function test_la_contrasena_usa_un_hash_bcrypt(): void
    {
        $this->assertSame('bcrypt', Hash::info($this->usuario('Clave-ficticia-1')->contrasena_hash)['algoName']);
    }

    public function test_el_hash_no_aparece_al_serializar_el_usuario(): void
    {
        $this->assertArrayNotHasKey('contrasena_hash', $this->usuario('Clave-ficticia-1')->toArray());
    }
}
