<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnostico';

    protected $primaryKey = 'id_diagnostico';

    public $timestamps = false;

    protected $fillable = ['id_servicio', 'resultado', 'fallas_identificadas', 'pruebas_realizadas', 'observaciones'];
}
