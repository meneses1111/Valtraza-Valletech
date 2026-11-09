<?php

use App\Models\Cliente;
use App\Models\Diagnostico;
use App\Models\Equipo;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\Trabajo;
use App\Models\Usuario;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$clave = getenv('VALTRAZA_CARGA_CLAVE');
if (! $clave || app()->environment('production') || ! str_contains(config('database.connections.pgsql.database'), 'pruebas')) {
    fwrite(STDERR, "Se requiere VALTRAZA_CARGA_CLAVE y una base de pruebas.\n");
    exit(2);
}

if (Tecnico::query()->where('numero_identificacion', 'like', 'CARGA-T%')->exists()) {
    fwrite(STDERR, "Los datos de carga ya existen.\n");
    exit(0);
}

mt_srand(20261109);
$nombres = ['Ana', 'Luis', 'María', 'Carlos', 'Sofía', 'Jorge', 'Paula', 'Andrés', 'Lucía', 'Diego'];
$apellidos = ['Gómez', 'Rodríguez', 'López', 'Martínez', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Díaz', 'Vargas'];
$estados = ['pendiente', 'en_curso', 'completado'];
$hash = Hash::make($clave);

DB::transaction(function () use ($nombres, $apellidos, $estados, $hash): void {
    $tecnicos = [];
    foreach (range(1, 10) as $n) {
        $tecnico = Tecnico::query()->create(['nombre' => 'Técnico Carga '.$n, 'numero_identificacion' => sprintf('CARGA-T%03d', $n)]);
        $usuario = Usuario::query()->create(['nombre_usuario' => sprintf('carga%02d', $n), 'rol' => 'tecnico', 'id_tecnico' => $tecnico->id_tecnico, 'codigo_activacion' => Str::random(32)]);
        $usuario->activado = true;
        $usuario->contrasena_hash = $hash;
        $usuario->save();
        $tecnicos[] = $tecnico;
    }

    foreach (range(1, 300) as $n) {
        $cliente = Cliente::query()->create([
            'nombre' => $nombres[$n % 10].' '.$apellidos[intdiv($n, 10) % 10].' Carga',
            'numero_identificacion' => sprintf('CARGA-C%05d', $n),
            'correo' => $n % 3 === 0 ? sprintf('cliente%05d@ejemplo.test', $n) : null,
        ]);

        foreach (range(1, 1 + $n % 2) as $e) {
            $equipo = Equipo::query()->create(['id_cliente' => $cliente->id_cliente, 'fecha_registro' => '2026-0'.(1 + $n % 9).'-1'.$e, 'modelo' => 'Modelo ficticio '.($n % 25)]);

            foreach (range(1, 2 + $n % 2) as $s) {
                $estado = $estados[($n + $s) % 3];
                $tecnico = $estado === 'pendiente' && $s % 2 === 0 ? null : $tecnicos[($n + $e + $s) % 10];
                $servicio = Servicio::query()->create([
                    'id_equipo' => $equipo->id_equipo,
                    'id_tecnico' => $tecnico?->id_tecnico,
                    'fecha' => sprintf('2026-%02d-%02d', 1 + ($n + $s) % 11, 1 + ($n * $s) % 27),
                    'nombre' => ['Mantenimiento preventivo', 'Cambio de pantalla', 'Revisión de arranque', 'Formateo e instalación'][($n + $s) % 4],
                    'descripcion_problema' => 'Problema ficticio número '.$n.'-'.$e.'-'.$s,
                    'estado' => $estado,
                ]);

                if ($estado !== 'pendiente') {
                    Diagnostico::query()->create(['id_servicio' => $servicio->id_servicio, 'resultado' => 'Diagnóstico ficticio '.$servicio->id_servicio]);
                    Trabajo::query()->create(['id_servicio' => $servicio->id_servicio, 'tipo' => $s % 2 ? 'mantenimiento' : 'reparacion', 'descripcion' => 'Actividad ficticia']);
                    Repuesto::query()->create(['id_servicio' => $servicio->id_servicio, 'nombre' => 'Repuesto ficticio', 'cantidad' => 1 + $s]);
                }
            }
        }
    }
});

foreach (['cliente', 'tecnico', 'usuario', 'equipo', 'servicio', 'diagnostico', 'trabajo', 'repuesto'] as $tabla) {
    echo str_pad($tabla, 12).DB::table($tabla)->count().PHP_EOL;
}
