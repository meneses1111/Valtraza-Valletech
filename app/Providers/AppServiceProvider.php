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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        config(['hashing.bcrypt.limit' => 72, 'session.http_only' => true, 'session.same_site' => 'lax', 'database.connections.pgsql.timezone' => 'UTC']);

        if ($this->app->environment('production')) {
            config(['app.debug' => false, 'session.secure' => true]);
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
        RateLimiter::for('consulta-publica', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
        RateLimiter::for('acceso', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
        Equipo::observe(EquipoObserver::class);
        Servicio::observe(ServicioObserver::class);
        Diagnostico::observe(AvanceObserver::class);
        Trabajo::observe(AvanceObserver::class);
        Repuesto::observe(AvanceObserver::class);
    }
}
