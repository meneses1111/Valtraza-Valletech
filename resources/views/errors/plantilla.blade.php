<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('titulo') · Valtraza</title><link rel="stylesheet" href="/assets/bootstrap.min.css"><link rel="stylesheet" href="/assets/valtraza.css"></head>
<body><main class="container py-5"><div class="card formulario-centrado"><div class="card-body p-lg-5"><p class="text-primary fw-semibold">Valtraza · @yield('codigo')</p><h1 class="h3">@yield('titulo')</h1><p class="text-body-secondary mt-3">@yield('mensaje')</p><div class="d-flex flex-wrap gap-3 mt-4"><a class="btn btn-primary" href="/">Volver al inicio</a><a class="btn btn-outline-secondary" href="/ingresar">Ingresar al sistema</a></div></div></div></main></body>
</html>
