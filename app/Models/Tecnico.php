<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    protected $table = 'tecnico';

    protected $primaryKey = 'id_tecnico';

    public $timestamps = false;

    protected $fillable = ['nombre', 'numero_identificacion', 'correo'];
}
