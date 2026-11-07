@extends('layouts.base')

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->nombre_usuario }}</h1>
    @if (auth()->user()->rol === 'administrador')
        <a href="{{ route('cuentas.crear') }}">Crear cuenta de técnico</a>
    @endif
    <form method="post" action="{{ route('salir') }}">@csrf<button type="submit">Salir</button></form>
@endsection
