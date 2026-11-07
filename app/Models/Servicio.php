<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $table = 'servicio';

    protected $primaryKey = 'id_servicio';

    public $timestamps = false;

    protected $fillable = ['codigo_servicio', 'codigo_seguimiento', 'id_equipo', 'id_tecnico', 'fecha', 'nombre', 'descripcion_problema', 'estado'];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo', 'id_equipo');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class, 'id_tecnico', 'id_tecnico');
    }

    public function diagnosticos(): HasMany
    {
        return $this->hasMany(Diagnostico::class, 'id_servicio', 'id_servicio')->orderBy('id_diagnostico');
    }

    public function trabajos(): HasMany
    {
        return $this->hasMany(Trabajo::class, 'id_servicio', 'id_servicio')->orderBy('id_trabajo');
    }

    public function repuestos(): HasMany
    {
        return $this->hasMany(Repuesto::class, 'id_servicio', 'id_servicio')->orderBy('id_repuesto');
    }
}
