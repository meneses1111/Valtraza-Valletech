<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indices = [
        'cliente' => ['idx_cliente_nombre_id' => ['nombre', 'id_cliente']],
        'tecnico' => ['idx_tecnico_nombre_id' => ['nombre', 'id_tecnico']],
        'servicio' => ['idx_servicio_equipo_fecha_id' => ['id_equipo', 'fecha', 'id_servicio']],
        'diagnostico' => ['idx_diagnostico_servicio_id' => ['id_servicio', 'id_diagnostico']],
        'trabajo' => ['idx_trabajo_servicio_id' => ['id_servicio', 'id_trabajo']],
        'repuesto' => ['idx_repuesto_servicio_id' => ['id_servicio', 'id_repuesto']],
    ];

    public function up(): void
    {
        foreach ($this->indices as $tabla => $indices) {
            foreach ($indices as $nombre => $columnas) {
                Schema::table($tabla, fn (Blueprint $table) => $table->index($columnas, $nombre));
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indices as $tabla => $indices) {
            foreach (array_keys($indices) as $nombre) {
                Schema::table($tabla, fn (Blueprint $table) => $table->dropIndex($nombre));
            }
        }
    }
};
