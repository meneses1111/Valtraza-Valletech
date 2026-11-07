<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    protected $table = 'trabajo';

    protected $primaryKey = 'id_trabajo';

    public $timestamps = false;

    protected $fillable = ['id_servicio', 'tipo', 'descripcion', 'observaciones'];
}
