<?php

namespace App\Providers;

use App\Models\Diagnostico;
use App\Models\Equipo;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\Usuario;
use App\Observers\AvanceObserver;
use App\Observers\EquipoObserver;
use App\Observers\ServicioObserver;
use App\Observers\UsuarioObserver;
use App\Support\BuscadorVistas;
use App\Support\EntradaFormulario;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('view.finder', fn ($app) => new BuscadorVistas(
            $app['files'], $app['config']['view.paths']
        ));
    }

    public function boot(): void
    {
        config(['hashing.bcrypt.limit' => 72, 'session.http_only' => true, 'session.same_site' => 'lax', 'database.connections.pgsql.timezone' => 'UTC']);

        if ($this->app->environment('production')) {
            config(['app.debug' => false, 'session.secure' => true]);
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
        Paginator::currentPageResolver(function (string $nombre = 'page'): int {
            $valor = request()->query($nombre, 1);
            $pagina = is_int($valor) || is_string($valor)
                ? filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => EntradaFormulario::MAX_PAGINA]])
                : false;
            abort_if($pagina === false, 404);

            return $pagina;
        });
        foreach (['cliente', 'servicio'] as $parametro) {
            Route::bind($parametro, function (string $valor): int {
                $identificador = EntradaFormulario::identificador($valor);
                abort_if($identificador === null, 404);

                return $identificador;
            });
        }

        RateLimiter::for('consulta-publica', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('acceso', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        Usuario::observe(UsuarioObserver::class);
        Equipo::observe(EquipoObserver::class);
        Servicio::observe(ServicioObserver::class);
        Diagnostico::observe(AvanceObserver::class);
        Trabajo::observe(AvanceObserver::class);
        Repuesto::observe(AvanceObserver::class);
    }
}
