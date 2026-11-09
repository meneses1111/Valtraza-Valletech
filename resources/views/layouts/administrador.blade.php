@extends('layouts.base')

@section('navegacion')
    @if(\Illuminate\Support\Facades\Route::has('servicios.index'))<a class="nav-link" href="{{ route('servicios.index') }}">Servicios</a>@endif
    @if(\Illuminate\Support\Facades\Route::has('servicios.crear'))<a class="nav-link" href="{{ route('servicios.crear') }}">Nuevo servicio</a>@endif
    @if(\Illuminate\Support\Facades\Route::has('clientes.index'))<a class="nav-link" href="{{ route('clientes.index') }}">Clientes</a>@endif
    @if(\Illuminate\Support\Facades\Route::has('tecnicos.index'))<a class="nav-link" href="{{ route('tecnicos.index') }}">Técnicos</a>@endif
    <a class="nav-link" href="{{ route('cuentas.crear') }}">Cuentas</a>
@endsection
