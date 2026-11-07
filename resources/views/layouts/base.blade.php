<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Valtraza</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap.min.css') }}">
</head>
<body>
    <header class="container py-3"><a class="navbar-brand" href="{{ auth()->check() ? route('panel') : route('login') }}">Valtraza</a></header>
    <main class="container py-4">
        @if (session('aviso')) <p role="status">{{ session('aviso') }}</p> @endif
        @yield('contenido')
    </main>
</body>
</html>
