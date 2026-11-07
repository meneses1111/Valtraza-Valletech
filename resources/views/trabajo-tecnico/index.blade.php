@extends('layouts.tecnico')

@section('contenido')
<h1 class="mb-4">Mis servicios</h1>
<div class="card"><div class="card-body">
    <form method="get" action="{{ route('mis-servicios.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-sm-5"><label class="form-label" for="estado">Estado</label><select class="form-select" id="estado" name="estado"><option value="">Todos los estados</option>@foreach(\App\Enums\EstadoServicio::cases() as $estado)<option value="{{ $estado->value }}" @selected(request('estado') === $estado->value)>{{ $estado->etiqueta() }}</option>@endforeach</select></div>
        <div class="col-sm-auto"><button class="btn btn-outline-primary" type="submit">Filtrar</button></div>
    </form>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Código</th><th>Servicio</th><th>Computador</th><th>Fecha</th><th>Estado</th><th>Detalle</th></tr></thead><tbody>
        @forelse($servicios as $servicio)<tr><td>{{ $servicio->codigo_servicio }}</td><td>{{ $servicio->nombre }}</td><td>{{ $servicio->equipo->codigo_equipo }} · {{ $servicio->equipo->modelo }}</td><td>{{ \Illuminate\Support\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td><td><x-estado :valor="$servicio->estado" /></td><td><a href="{{ route('mis-servicios.ver', $servicio->id_servicio) }}">Ver servicio</a></td></tr>
        @empty<tr><td colspan="6" class="py-4">{{ request('estado') ? 'No hay servicios con ese filtro. Prueba con otro estado.' : 'Todavía no tienes servicios asignados.' }}</td></tr>@endforelse
    </tbody></table></div>{{ $servicios->links() }}
</div></div>
@endsection
