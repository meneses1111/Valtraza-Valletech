<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = 'repuesto';

    protected $primaryKey = 'id_repuesto';

    public $timestamps = false;

    protected $fillable = ['id_servicio', 'nombre', 'cantidad', 'observacion'];
}
