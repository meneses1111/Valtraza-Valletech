@extends('layouts.administrador')

@section('contenido')
    <div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Administrador</p><h1>Servicios</h1></div><a class="btn btn-primary" href="{{ route('servicios.crear') }}">Nuevo servicio</a></div>
    <div class="card"><div class="card-body">
        <form method="get" action="{{ route('servicios.index') }}" class="row g-2 align-items-end mb-4">
            <div class="col-sm-5 col-lg-4"><label for="asignacion" class="form-label">Asignación de técnico</label><select id="asignacion" name="asignacion" class="form-select"><option value="">Todos los servicios</option><option value="sin_tecnico" @selected(request('asignacion') === 'sin_tecnico')>Sin técnico asignado</option><option value="con_tecnico" @selected(request('asignacion') === 'con_tecnico')>Con técnico asignado</option></select></div>
            <div class="col-sm-auto"><button class="btn btn-outline-primary" type="submit">Filtrar</button></div>
        </form>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Código</th><th>Servicio / cliente</th><th>Computador</th><th>Técnico</th><th>Estado</th><th>Detalle</th></tr></thead><tbody>
            @forelse($servicios as $servicio)
                <tr><td>{{ $servicio->codigo_servicio }}</td><td>{{ $servicio->nombre }}<br><span class="text-body-secondary small">{{ $servicio->equipo->cliente->nombre }}</span></td><td>{{ $servicio->equipo->codigo_equipo }}</td><td>{{ $servicio->tecnico?->nombre ?: 'Sin asignar' }}</td><td><x-estado :valor="$servicio->estado" /></td><td><a href="{{ route('servicios.ver', $servicio->id_servicio) }}">Ver servicio</a></td></tr>
            @empty
                <tr><td colspan="6" class="py-4">No hay servicios con esos filtros. Cambia o limpia el filtro.</td></tr>
            @endforelse
        </tbody></table></div>
        {{ $servicios->links() }}
    </div></div>
@endsection
