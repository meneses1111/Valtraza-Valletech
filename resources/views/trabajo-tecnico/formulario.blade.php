@extends('layouts.tecnico')

@section('contenido')
<a class="d-inline-block mb-3" href="{{ route('mis-servicios.ver', $servicio->id_servicio) }}">Volver al servicio</a>
<h1 class="h3 mb-4">@yield('titulo_formulario')</h1>
<x-resumen-servicio :servicio="$servicio" />
<div class="card formulario-centrado"><div class="card-body">@yield('formulario')</div></div>
@endsection
