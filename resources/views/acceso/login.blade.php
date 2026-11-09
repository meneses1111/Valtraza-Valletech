@extends('layouts.publica')

@section('contenido')
<div class="card formulario-centrado"><div class="card-body p-lg-4">
    <h1 class="h3">Ingresar a Valtraza</h1><p class="text-body-secondary">Acceso para el administrador y los técnicos registrados.</p>
    <form method="post" action="{{ route('login.iniciar') }}">@csrf
        <x-campo nombre="nombre_usuario" etiqueta="Usuario *" maxlength="50" autocomplete="username" required />
        <x-campo nombre="password" etiqueta="Contraseña *" tipo="password" autocomplete="current-password" required />
        <button class="btn btn-primary" type="submit">Ingresar</button>
    </form>
    <p class="text-body-secondary mt-3 mb-2">Si todavía no activas tu cuenta, usa <a href="{{ route('activar') }}">Activar mi cuenta</a> con el código que te entregó el administrador.</p>
    <div class="d-flex flex-wrap gap-3"><a href="{{ route('inicio') }}">Consultar un servicio</a></div>
</div></div>
@endsection
