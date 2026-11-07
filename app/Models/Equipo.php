<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipo';

    protected $primaryKey = 'id_equipo';

    public $timestamps = false;

    protected $fillable = ['codigo_equipo', 'id_cliente', 'fecha_registro', 'modelo', 'otros_datos_tecnicos'];
}
