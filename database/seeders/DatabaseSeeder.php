<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('usuario')->where('nombre_usuario', 'admin')->exists()) {
            return;
        }

        $codigo = Str::random(32);

        DB::table('usuario')->insert([
            'nombre_usuario' => 'admin',
            'rol' => 'administrador',
            'codigo_activacion' => $codigo,
            'activado' => false,
        ]);

        $this->command->info('Código de activación del administrador: '.$codigo);
    }
}
