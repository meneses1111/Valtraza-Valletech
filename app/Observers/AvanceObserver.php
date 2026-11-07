<?php

namespace App\Observers;

use App\Events\ServicioActualizado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AvanceObserver
{
    public function created(Model $registro): void
    {
        DB::afterCommit(fn () => event(new ServicioActualizado($registro->id_servicio)));
    }
}
