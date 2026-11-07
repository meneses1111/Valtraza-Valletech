<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'cliente';

    protected $primaryKey = 'id_cliente';

    public $timestamps = false;

    protected $fillable = ['nombre', 'numero_identificacion', 'correo'];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'id_cliente', 'id_cliente')->orderByDesc('id_equipo');
    }
}
