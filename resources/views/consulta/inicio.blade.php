@extends('layouts.publica')

@section('contenido')
<div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Mantenimiento y reparación de computadores</p><h1>Bienvenido a Valtraza</h1><p class="text-body-secondary">Consulta el avance de tu servicio con el código que recibiste.</p></div></div>
<div class="row g-4 align-items-start">
    <section class="col-lg-7" aria-labelledby="consulta-titulo"><div class="card"><div class="card-body p-lg-4">
        <h2 id="consulta-titulo" class="h4">Consultar mi servicio</h2><p>Escribe el código de seguimiento tal como aparece. Distingue mayúsculas y minúsculas.</p>
        <form method="post" action="{{ route('consulta.consultar') }}">@csrf
            <x-campo nombre="codigo_seguimiento" etiqueta="Código de seguimiento *" maxlength="32" autocomplete="off" autocapitalize="off" spellcheck="false" required />
            <button class="btn btn-primary" type="submit">Consultar servicio</button>
        </form>
    </div></div></section>
    <section class="col-lg-5" aria-labelledby="ingreso-titulo"><div class="card"><div class="card-body p-lg-4">
        <h2 id="ingreso-titulo" class="h4">Acceso al sistema</h2>
        @guest
            <p class="text-body-secondary">Administrador y técnicos registrados.</p>
            <form method="post" action="{{ route('login.iniciar') }}">@csrf
                <x-campo nombre="nombre_usuario" etiqueta="Usuario *" maxlength="50" autocomplete="username" required />
                <x-campo nombre="password" etiqueta="Contraseña *" tipo="password" autocomplete="current-password" required />
                <button class="btn btn-primary" type="submit">Ingresar</button>
            </form>
            <div class="d-flex flex-wrap gap-3 mt-3"><a href="{{ route('login') }}">Entrar como técnico</a><a href="{{ route('activar') }}">Activar mi cuenta</a></div>
        @else
            <p>Ya tienes una sesión iniciada.</p><a class="btn btn-primary" href="{{ route('panel') }}">Ir a mi panel</a>
        @endguest
    </div></div></section>
</div>
@endsection
