<?php

namespace App\Observers;

use App\Events\ServicioActualizado;
use App\Models\Servicio;
use App\Support\CodigosRegistro;
use Illuminate\Support\Facades\DB;

class ServicioObserver
{
    public function creating(Servicio $servicio): void
    {
        $codigos = app(CodigosRegistro::class);
        $servicio->codigo_servicio ??= $codigos->servicio();
        $servicio->codigo_seguimiento ??= $codigos->seguimiento();
        $servicio->estado ??= 'pendiente';
    }

    public function updated(Servicio $servicio): void
    {
        DB::afterCommit(fn () => event(new ServicioActualizado($servicio->id_servicio)));
    }
}
