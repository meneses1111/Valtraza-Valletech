@extends('layouts.administrador')

@section('contenido')
    <div class="cabecera-pagina"><div><a href="{{ route('clientes.index') }}">Volver a clientes</a><h1 class="mt-2">{{ $cliente->nombre }}</h1><p class="text-body-secondary">{{ $cliente->numero_identificacion }} · {{ $cliente->correo ?: 'Correo sin registrar' }}</p></div></div>
    <div class="row g-4">
        <section class="col-lg-8" aria-labelledby="equipos-cliente">
            <h2 id="equipos-cliente" class="h4">Computadores e historial</h2>
            @forelse($cliente->equipos as $equipo)
                <article class="card mb-3"><div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-2"><h3 class="h5">{{ $equipo->codigo_equipo }} · {{ $equipo->modelo ?: 'Modelo no registrado' }}</h3>@if(\Illuminate\Support\Facades\Route::has('servicios.crear'))<a href="{{ route('servicios.crear', ['id_cliente' => $cliente->id_cliente, 'id_equipo' => $equipo->id_equipo]) }}">Nuevo servicio</a>@endif</div>
                    <p class="small text-body-secondary">Registrado el {{ \Illuminate\Support\Carbon::parse($equipo->fecha_registro)->format('d/m/Y') }}</p>
                    @if($equipo->otros_datos_tecnicos)<p class="texto-multilinea">{{ $equipo->otros_datos_tecnicos }}</p>@endif
                    <div class="table-responsive"><table class="table table-sm"><caption>Historial de servicios de {{ $equipo->codigo_equipo }}</caption><thead><tr><th>Código</th><th>Servicio</th><th>Fecha</th><th>Estado</th></tr></thead><tbody>
                        @forelse($historialAgrupado->get($equipo->id_equipo, collect()) as $servicio)
                            <tr><td>{{ $servicio->codigo_servicio }}</td><td>{{ $servicio->nombre }}</td><td>{{ \Illuminate\Support\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td><td><x-estado :valor="$servicio->estado" /></td></tr>
                        @empty
                            <tr><td colspan="4">{{ $equipo->servicios_count > 0 ? 'Consulta las otras páginas del historial para ver los servicios de este computador.' : 'Este computador todavía no tiene servicios.' }}</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div></article>
            @empty
                <p class="alert alert-info">Este cliente todavía no tiene computadores registrados.</p>
            @endforelse
            {{ $equipos->links() }}
            {{ $historial->links() }}
        </section>
        @if(\Illuminate\Support\Facades\Route::has('equipos.guardar'))
        <section class="col-lg-4" aria-labelledby="nuevo-equipo">
            <div class="card"><div class="card-body"><h2 id="nuevo-equipo" class="h5">Nuevo computador</h2><p class="text-body-secondary small">Se asociará a {{ $cliente->nombre }}.</p>
                <form method="post" action="{{ route('equipos.guardar', $cliente->id_cliente) }}">@csrf
                    <x-campo nombre="fecha_registro" etiqueta="Fecha de registro *" tipo="date" :valor="now()->timezone('America/Bogota')->toDateString()" required />
                    <x-campo nombre="modelo" etiqueta="Modelo (opcional)" maxlength="100" />
                    <x-campo nombre="otros_datos_tecnicos" etiqueta="Otros datos técnicos (opcional)" tipo="textarea" />
                    <button class="btn btn-primary" type="submit">Registrar computador</button>
                </form>
            </div></div>
        </section>
        @endif
    </div>
@endsection
