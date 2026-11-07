<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = ['nombre_usuario', 'rol', 'codigo_activacion', 'id_tecnico'];

    protected $hidden = ['contrasena_hash', 'codigo_activacion', 'sal'];

    protected function casts(): array
    {
        return ['activado' => 'boolean'];
    }

    public function getAuthPassword(): string
    {
        return $this->contrasena_hash ?? '';
    }
}
