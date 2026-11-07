<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Valtraza</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/valtraza.css') }}">
    <script src="{{ asset('assets/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/valtraza.js') }}" defer></script>
</head>
<body>
    <header class="cabecera"><div class="container py-3 d-flex flex-wrap align-items-center gap-3"><a class="navbar-brand" href="{{ auth()->check() ? route('panel') : route('login') }}">Valtraza</a><nav class="nav flex-grow-1" aria-label="Menú principal">@yield('navegacion')</nav>@auth<form method="post" action="{{ route('salir') }}">@csrf<button class="btn btn-outline-secondary btn-sm" type="submit">Cerrar sesión</button></form>@endauth</div></header>
    <main class="container py-4">
        @if (session('aviso')) <p class="alert alert-success" role="status">{{ session('aviso') }}</p> @endif
        @if ($errors->any())<div class="alert alert-danger" role="alert"><p class="fw-semibold mb-1">Revisa la información antes de continuar.</p><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('contenido')
    </main>
    <div class="modal fade" id="confirmacion" tabindex="-1" aria-labelledby="confirmacion-titulo" aria-hidden="true" data-mostrar="{{ session('confirmacion') ? 'si' : 'no' }}"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-5" id="confirmacion-titulo">{{ session('confirmacion', 'Confirmación') }}</h2></div><div class="modal-body">Operación completada.</div><div class="modal-footer"><button class="btn btn-primary" type="button" data-bs-dismiss="modal">Aceptar</button></div></div></div></div>
</body>
</html>
