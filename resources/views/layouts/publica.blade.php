@extends('layouts.base')

@section('navegacion')
    <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
    @guest<a class="nav-link" href="{{ route('login') }}">Ingresar</a><a class="nav-link" href="{{ route('activar') }}">Activar mi cuenta</a>@endguest
    @auth<a class="nav-link" href="{{ route('panel') }}">Mi panel</a>@endauth
@endsection
