<?php

namespace Tests\Concerns;

use App\Models\Cliente;
use App\Models\Diagnostico;
use App\Models\Equipo;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\Trabajo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait DatosDePrueba
{
    protected string $contrasenaDePrueba = 'Clave-ficticia-1';

    protected function administrador(string $nombre = 'admin'): Usuario
    {
        $existente = Usuario::query()->where('nombre_usuario', $nombre)->first();

        if ($existente) {
            return $existente;
        }

        $usuario = Usuario::query()->create([
            'nombre_usuario' => $nombre,
            'rol' => 'administrador',
            'codigo_activacion' => Str::random(32),
        ]);
        $usuario->activado = true;
        $usuario->contrasena_hash = Hash::make($this->contrasenaDePrueba);
        $usuario->save();

        return $usuario;
    }

    protected function tecnico(string $nombre = 'Técnica Ficticia', ?string $identificacion = null): Tecnico
    {
        return Tecnico::query()->create([
            'nombre' => $nombre,
            'numero_identificacion' => $identificacion ?? 'T'.Str::random(10),
        ]);
    }

    protected function cuentaTecnico(?Tecnico $tecnico = null, ?string $nombreUsuario = null, bool $activa = true): Usuario
    {
        $tecnico ??= $this->tecnico();
        $usuario = Usuario::query()->create([
            'nombre_usuario' => $nombreUsuario ?? 'tec'.$tecnico->id_tecnico,
            'rol' => 'tecnico',
            'id_tecnico' => $tecnico->id_tecnico,
            'codigo_activacion' => Str::random(32),
        ]);

        if ($activa) {
            $usuario->activado = true;
            $usuario->contrasena_hash = Hash::make($this->contrasenaDePrueba);
            $usuario->save();
        }

        return $usuario;
    }

    protected function tecnicoDe(Usuario $cuenta): Tecnico
    {
        return Tecnico::query()->findOrFail($cuenta->id_tecnico);
    }

    protected function cliente(string $nombre = 'Cliente Ficticio', ?string $identificacion = null, ?string $correo = null): Cliente
    {
        return Cliente::query()->create([
            'nombre' => $nombre,
            'numero_identificacion' => $identificacion ?? 'C'.Str::random(10),
            'correo' => $correo,
        ]);
    }

    protected function equipo(?Cliente $cliente = null, ?string $modelo = 'Portátil de prueba X1'): Equipo
    {
        $cliente ??= $this->cliente();

        return Equipo::query()->create([
            'id_cliente' => $cliente->id_cliente,
            'fecha_registro' => '2026-11-01',
            'modelo' => $modelo,
        ]);
    }

    protected function servicio(?Equipo $equipo = null, ?Tecnico $tecnico = null, string $estado = 'pendiente', array $datos = []): Servicio
    {
        $equipo ??= $this->equipo();

        return Servicio::query()->create([
            'id_equipo' => $equipo->id_equipo,
            'id_tecnico' => $tecnico?->id_tecnico,
            'fecha' => '2026-11-05',
            'nombre' => 'Mantenimiento general',
            'descripcion_problema' => 'El equipo se apaga solo',
            'estado' => $estado,
            ...$datos,
        ]);
    }

    protected function diagnostico(Servicio $servicio, string $resultado = 'Ventilador obstruido'): Diagnostico
    {
        return Diagnostico::query()->create([
            'id_servicio' => $servicio->id_servicio,
            'resultado' => $resultado,
            'fallas_identificadas' => $resultado,
        ]);
    }

    protected function trabajo(Servicio $servicio, string $tipo = 'mantenimiento', string $descripcion = 'Limpieza interna'): Trabajo
    {
        return Trabajo::query()->create([
            'id_servicio' => $servicio->id_servicio,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
        ]);
    }

    protected function repuesto(Servicio $servicio, string $nombre = 'Pasta térmica', int $cantidad = 1): Repuesto
    {
        return Repuesto::query()->create([
            'id_servicio' => $servicio->id_servicio,
            'nombre' => $nombre,
            'cantidad' => $cantidad,
        ]);
    }
}
