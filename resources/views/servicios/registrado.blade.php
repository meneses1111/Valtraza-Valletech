@extends('layouts.administrador')

@section('contenido')
    <div class="card formulario-centrado"><div class="card-body p-lg-4">
        <p class="text-success fw-semibold">Servicio registrado correctamente</p><h1 class="h3">{{ $servicio->nombre }}</h1>
        <dl class="row mt-4"><dt class="col-sm-4">Código del servicio</dt><dd class="col-sm-8">{{ $servicio->codigo_servicio }}</dd><dt class="col-sm-4">Estado inicial</dt><dd class="col-sm-8"><x-estado :valor="$servicio->estado" /></dd></dl>
        <div class="p-3 bg-body-tertiary rounded mb-4"><p class="fw-semibold mb-1">Código de seguimiento para el cliente</p><p class="codigo-seguimiento mb-2">{{ $servicio->codigo_seguimiento }}</p><button class="btn btn-outline-primary btn-sm" type="button" data-copiar="{{ $servicio->codigo_seguimiento }}">Copiar código</button><p class="small mb-0 mt-2">Entrega este código al cliente para consultar su servicio.</p></div>
        <a class="btn btn-primary" href="{{ route('servicios.asignacion', $servicio->id_servicio) }}">Asignar técnico</a><a class="btn btn-outline-secondary" href="{{ route('servicios.index') }}">Ver servicios</a>
    </div></div>
@endsection
