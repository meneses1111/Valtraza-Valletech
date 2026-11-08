<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS idx_equipo_cliente ON equipo (id_cliente)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_servicio_equipo ON servicio (id_equipo)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_diagnostico_servicio ON diagnostico (id_servicio)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_trabajo_servicio ON trabajo (id_servicio)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_repuesto_servicio ON repuesto (id_servicio)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_servicio_tecnico_estado_fecha ON servicio (id_tecnico, estado, fecha)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_servicio_estado_fecha ON servicio (estado, fecha)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_servicio_sin_tecnico ON servicio (fecha DESC) WHERE id_tecnico IS NULL');
    }

    public function down(): void
    {
        foreach ([
            'idx_servicio_sin_tecnico', 'idx_servicio_estado_fecha', 'idx_servicio_tecnico_estado_fecha',
            'idx_repuesto_servicio', 'idx_trabajo_servicio', 'idx_diagnostico_servicio',
            'idx_servicio_equipo', 'idx_equipo_cliente',
        ] as $indice) {
            DB::statement('DROP INDEX IF EXISTS '.$indice);
        }
    }
};
