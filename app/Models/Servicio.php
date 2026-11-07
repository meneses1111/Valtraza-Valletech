<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicio';

    protected $primaryKey = 'id_servicio';

    public $timestamps = false;

    protected $fillable = ['codigo_servicio', 'codigo_seguimiento', 'id_equipo', 'id_tecnico', 'fecha', 'nombre', 'descripcion_problema', 'estado'];
}
