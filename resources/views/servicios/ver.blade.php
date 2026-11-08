@extends('layouts.administrador')

@section('contenido')
    <a class="d-inline-block mb-3" href="{{ route('servicios.index') }}">Volver a servicios</a>
    <x-resumen-servicio :servicio="$servicio" />
    <div class="card"><div class="card-body"><dl class="row"><dt class="col-sm-3">Cliente</dt><dd class="col-sm-9">{{ $servicio->equipo->cliente->nombre }}</dd><dt class="col-sm-3">Identificación del cliente</dt><dd class="col-sm-9">{{ $servicio->equipo->cliente->numero_identificacion }}</dd><dt class="col-sm-3">Correo del cliente</dt><dd class="col-sm-9">{{ $servicio->equipo->cliente->correo ?: 'No registrado' }}</dd><dt class="col-sm-3">Datos técnicos del computador</dt><dd class="col-sm-9 texto-multilinea">{{ $servicio->equipo->otros_datos_tecnicos ?: 'No registrados' }}</dd><dt class="col-sm-3">Técnico encargado</dt><dd class="col-sm-9">{{ $servicio->tecnico?->nombre ?: 'Sin técnico asignado' }}</dd><dt class="col-sm-3">Código de seguimiento</dt><dd class="col-sm-9 codigo-seguimiento">{{ $servicio->codigo_seguimiento }}</dd></dl>
        <a class="btn btn-primary" href="{{ route('servicios.asignacion', $servicio->id_servicio) }}">Asignar / cambiar técnico</a>
    </div></div>
    <x-avance-administrador :avance="$avance" />
@endsection
