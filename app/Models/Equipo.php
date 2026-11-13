<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipo';

    protected $primaryKey = 'id_equipo';

    public $timestamps = false;

    protected $fillable = ['codigo_equipo', 'id_cliente', 'fecha_registro', 'modelo', 'otros_datos_tecnicos'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'id_equipo', 'id_equipo');
    }
}
