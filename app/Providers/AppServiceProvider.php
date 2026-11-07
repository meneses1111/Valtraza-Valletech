<?php

namespace App\Providers;

use App\Models\Diagnostico;
use App\Models\Equipo;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Observers\AvanceObserver;
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
        Diagnostico::observe(AvanceObserver::class);
        Trabajo::observe(AvanceObserver::class);
        Repuesto::observe(AvanceObserver::class);
    }
}
