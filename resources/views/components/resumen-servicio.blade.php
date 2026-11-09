@props(['servicio'])

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <div><p class="text-body-secondary mb-1">{{ $servicio->codigo_servicio }}</p><h1 class="h3 mb-0">{{ $servicio->nombre }}</h1></div>
            <x-estado :valor="$servicio->estado" />
        </div>
        <dl class="row mb-0">
            <dt class="col-sm-3">Computador</dt><dd class="col-sm-9">{{ $servicio->equipo->codigo_equipo }} · {{ $servicio->equipo->modelo ?: 'Modelo no registrado' }}</dd>
            <dt class="col-sm-3">Fecha del servicio</dt><dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</dd>
            <dt class="col-sm-3">Problema presentado</dt><dd class="col-sm-9 texto-multilinea">{{ $servicio->descripcion_problema }}</dd>
        </dl>
    </div>
</div>
