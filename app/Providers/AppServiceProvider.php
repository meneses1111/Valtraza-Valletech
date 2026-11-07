<?php

namespace App\Providers;

use App\Models\Equipo;
use App\Observers\EquipoObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Equipo::observe(EquipoObserver::class);
    }
}
