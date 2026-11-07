@extends('layouts.tecnico')

@section('contenido')
<a class="d-inline-block mb-3" href="{{ route('mis-servicios.index') }}">Volver a mis servicios</a>
<x-resumen-servicio :servicio="$servicio" />
<div class="d-flex justify-content-end mb-3">@if(\Illuminate\Support\Facades\Route::has('estado.crear'))<a class="btn btn-outline-primary" href="{{ route('estado.crear', $servicio->id_servicio) }}">Cambiar estado</a>@endif</div>
<div class="card"><div class="card-body">
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-diagnosticos" data-bs-toggle="tab" data-bs-target="#diagnosticos" type="button" role="tab" aria-controls="diagnosticos" aria-selected="true">Diagnósticos</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-trabajos" data-bs-toggle="tab" data-bs-target="#trabajos" type="button" role="tab" aria-controls="trabajos" aria-selected="false">Mantenimientos y reparaciones</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-repuestos" data-bs-toggle="tab" data-bs-target="#repuestos" type="button" role="tab" aria-controls="repuestos" aria-selected="false">Repuestos</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="tab-historial" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab" aria-controls="historial" aria-selected="false">Historial del equipo</button></li>
</ul>
<div class="tab-content">
    <section class="tab-pane fade show active" id="diagnosticos" role="tabpanel" aria-labelledby="tab-diagnosticos" tabindex="0">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h2 class="h5">Diagnósticos registrados</h2>@if(\Illuminate\Support\Facades\Route::has('diagnosticos.crear'))<a class="btn btn-primary btn-sm" href="{{ route('diagnosticos.crear', $servicio->id_servicio) }}">Registrar diagnóstico</a>@endif</div>
        @forelse($servicio->diagnosticos as $diagnostico)<article class="border rounded p-3 mb-3"><h3 class="h6">{{ \Illuminate\Support\Carbon::parse($diagnostico->fecha_registro)->timezone('America/Bogota')->format('d/m/Y H:i') }}</h3><p class="texto-multilinea">{{ $diagnostico->resultado }}</p>@if($diagnostico->pruebas_realizadas)<p class="texto-multilinea"><strong>Pruebas realizadas:</strong> {{ $diagnostico->pruebas_realizadas }}</p>@endif @if($diagnostico->observaciones)<p class="texto-multilinea mb-0"><strong>Observaciones:</strong> {{ $diagnostico->observaciones }}</p>@endif</article>
        @empty<p class="text-body-secondary py-3">Todavía no hay diagnósticos registrados.</p>@endforelse
    </section>
    <section class="tab-pane fade" id="trabajos" role="tabpanel" aria-labelledby="tab-trabajos" tabindex="0">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h2 class="h5">Mantenimientos y reparaciones</h2>@if(\Illuminate\Support\Facades\Route::has('trabajos.crear'))<a class="btn btn-primary btn-sm" href="{{ route('trabajos.crear', $servicio->id_servicio) }}">Registrar actividad</a>@endif</div>
        @forelse($servicio->trabajos as $trabajo)<article class="border rounded p-3 mb-3"><h3 class="h6">{{ \App\Enums\TipoTrabajo::from($trabajo->tipo)->etiqueta() }}</h3><p class="texto-multilinea">{{ $trabajo->descripcion }}</p>@if($trabajo->observaciones)<p class="texto-multilinea mb-0"><strong>Observaciones:</strong> {{ $trabajo->observaciones }}</p>@endif</article>@empty<p class="text-body-secondary py-3">Todavía no hay actividades registradas.</p>@endforelse
    </section>
    <section class="tab-pane fade" id="repuestos" role="tabpanel" aria-labelledby="tab-repuestos" tabindex="0">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h2 class="h5">Repuestos utilizados</h2>@if(\Illuminate\Support\Facades\Route::has('repuestos.crear'))<a class="btn btn-primary btn-sm" href="{{ route('repuestos.crear', $servicio->id_servicio) }}">Registrar repuestos</a>@endif</div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Nombre</th><th>Cantidad</th><th>Observaciones</th></tr></thead><tbody>@forelse($servicio->repuestos as $repuesto)<tr><td>{{ $repuesto->nombre }}</td><td>{{ $repuesto->cantidad }}</td><td>{{ $repuesto->observacion ?: 'Sin observaciones' }}</td></tr>@empty<tr><td colspan="3">Todavía no hay repuestos registrados.</td></tr>@endforelse</tbody></table></div>
    </section>
    <section class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="tab-historial" tabindex="0">
        <h2 class="h5">Servicios anteriores completados</h2><p class="text-body-secondary small">Historial de referencia del computador, de solo lectura.</p>
        @forelse($historial as $anterior)<article class="border rounded p-3 mb-3"><h3 class="h6">{{ $anterior->codigo_servicio }} · {{ $anterior->nombre }}</h3><p class="small">{{ \Illuminate\Support\Carbon::parse($anterior->fecha)->format('d/m/Y') }}</p>@forelse($anterior->trabajos as $trabajo)<p class="texto-multilinea"><strong>{{ \App\Enums\TipoTrabajo::from($trabajo->tipo)->etiqueta() }}:</strong> {{ $trabajo->descripcion }}@if($trabajo->observaciones)<br>{{ $trabajo->observaciones }}@endif</p>@empty<p>Este servicio no tiene mantenimientos ni reparaciones registrados.</p>@endforelse</article>@empty<p class="text-body-secondary py-3">Este computador no tiene servicios anteriores completados.</p>@endforelse
    </section>
</div>
</div></div>
@endsection
