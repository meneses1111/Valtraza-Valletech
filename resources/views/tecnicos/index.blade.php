@extends('layouts.administrador')

@section('contenido')
    <div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Administrador</p><h1>Técnicos</h1></div></div>
    <div class="row g-4">
        <section class="col-lg-7" aria-labelledby="lista-tecnicos">
            <div class="card"><div class="card-body"><h2 id="lista-tecnicos" class="h5">Técnicos registrados</h2>
                <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Identificación</th><th>Correo</th></tr></thead><tbody>
                    @forelse($tecnicos as $tecnico)
                        <tr><td>{{ $tecnico->nombre }}</td><td>{{ $tecnico->numero_identificacion }}</td><td>{{ $tecnico->correo ?: 'Sin registrar' }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-body-secondary py-4">Todavía no hay técnicos registrados.</td></tr>
                    @endforelse
                </tbody></table></div>
            </div></div>
        </section>
        <section class="col-lg-5" aria-labelledby="nuevo-tecnico">
            <div class="card"><div class="card-body"><h2 id="nuevo-tecnico" class="h5 mb-3">Nuevo técnico</h2>
                <form method="post" action="{{ route('tecnicos.guardar') }}">@csrf
                    <x-campo nombre="nombre" etiqueta="Nombre *" required maxlength="100" />
                    <x-campo nombre="numero_identificacion" etiqueta="Número de identificación *" required maxlength="20" />
                    <x-campo nombre="correo" etiqueta="Correo electrónico (opcional)" tipo="email" maxlength="100" />
                    <button class="btn btn-primary" type="submit">Registrar técnico</button>
                </form>
            </div></div>
        </section>
    </div>
@endsection
