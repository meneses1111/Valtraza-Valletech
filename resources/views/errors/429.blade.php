@extends('errors.plantilla')

@php
    $espera = isset($exception) && method_exists($exception, 'getHeaders') ? (int) ($exception->getHeaders()['Retry-After'] ?? 0) : 0;
@endphp

@section('codigo', '429')
@section('titulo', 'Demasiados intentos')
@section('mensaje')
    @if ($espera > 0)
        Por seguridad, espera antes de volver a intentarlo. Podrás intentarlo de nuevo en <strong class="cuenta-atras" data-cuenta-atras="{{ $espera }}">{{ $espera }} {{ $espera === 1 ? 'segundo' : 'segundos' }}</strong>.
    @else
        Espera un momento antes de volver a intentarlo.
    @endif
@endsection
