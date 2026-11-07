<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/esquema_valtraza.sql')));
    }

    public function down(): void
    {
        foreach (['repuesto', 'trabajo', 'diagnostico', 'servicio', 'equipo', 'usuario', 'tecnico', 'cliente'] as $tabla) {
            DB::statement('DROP TABLE IF EXISTS '.$tabla);
        }

        foreach (['tipo_trabajo', 'estado_servicio', 'rol_usuario'] as $tipo) {
            DB::statement('DROP TYPE IF EXISTS '.$tipo);
        }
    }
};
