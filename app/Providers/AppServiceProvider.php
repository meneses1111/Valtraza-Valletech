<?php

namespace App\Providers;

use App\Models\Equipo;
use App\Models\Servicio;
use App\Observers\EquipoObserver;
use App\Observers\ServicioObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Equipo::observe(EquipoObserver::class);
        Servicio::observe(ServicioObserver::class);
    }
}
