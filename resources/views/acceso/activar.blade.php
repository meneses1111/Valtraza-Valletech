@extends('layouts.publica')

@section('contenido')
<div class="card formulario-centrado"><div class="card-body p-lg-4">
    <h1 class="h3">Activar cuenta</h1><p class="text-body-secondary">Usa el código que te entregó el administrador y define tu contraseña.</p>
    <form method="post" action="{{ route('activar.guardar') }}">@csrf
        <x-campo nombre="codigo_activacion" etiqueta="Código de activación *" maxlength="64" autocomplete="off" autocapitalize="off" spellcheck="false" required />
        <x-campo nombre="password" etiqueta="Nueva contraseña *" tipo="password" minlength="8" autocomplete="new-password" required />
        <x-campo nombre="password_confirmation" etiqueta="Confirmar contraseña *" tipo="password" minlength="8" autocomplete="new-password" required />
        <button class="btn btn-primary" type="submit">Activar cuenta</button>
    </form>
    <a class="d-inline-block mt-3" href="{{ route('login') }}">Volver al inicio de sesión</a>
</div></div>
@endsection
