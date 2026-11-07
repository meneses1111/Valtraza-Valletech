@extends('layouts.administrador')

@section('contenido')
    <a class="d-inline-block mb-3" href="{{ route('servicios.index') }}">Volver a servicios</a>
    <x-resumen-servicio :servicio="$servicio" />
    <div class="card"><div class="card-body"><dl class="row"><dt class="col-sm-3">Cliente</dt><dd class="col-sm-9">{{ $servicio->equipo->cliente->nombre }}</dd><dt class="col-sm-3">Técnico encargado</dt><dd class="col-sm-9">{{ $servicio->tecnico?->nombre ?: 'Sin técnico asignado' }}</dd></dl>
        <a class="btn btn-primary" href="{{ route('servicios.asignacion', $servicio->id_servicio) }}">Asignar / cambiar técnico</a>
    </div></div>
@endsection
