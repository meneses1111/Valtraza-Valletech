@extends('layouts.publica')

@section('contenido')
<div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Consulta de seguimiento</p><h1>Estado de tu servicio</h1></div><a class="btn btn-outline-primary" href="{{ route('inicio') }}">Nueva consulta</a></div>
<article class="card formulario-centrado"><div class="card-body p-lg-4">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start"><h2 class="h3">{{ $resultado['nombre'] }}</h2><x-estado :valor="$resultado['estado']" /></div>
    <p class="mt-3"><strong>Computador:</strong> {{ $resultado['equipo_codigo'] }} · {{ $resultado['equipo_modelo'] ?: 'Modelo no registrado' }}</p>
    <hr><h3 class="h5 mb-3">Resumen del avance</h3>
    <dl class="row"><dt class="col-sm-5">Diagnóstico</dt><dd class="col-sm-7">{{ $resultado['avance']['diagnosticos'] ? 'Ya hay un diagnóstico registrado.' : 'Todavía no hay un diagnóstico registrado.' }}</dd><dt class="col-sm-5">Mantenimiento</dt><dd class="col-sm-7">{{ $resultado['avance']['mantenimientos'] ? 'Se registró una actividad de mantenimiento.' : 'Todavía no hay mantenimientos registrados.' }}</dd><dt class="col-sm-5">Reparación</dt><dd class="col-sm-7">{{ $resultado['avance']['reparaciones'] ? 'Se registró una actividad de reparación.' : 'Todavía no hay reparaciones registradas.' }}</dd></dl>
    <p class="text-body-secondary small mb-0">Esta consulta es informativa y muestra el avance guardado del servicio.</p>
</div></article>
@endsection
