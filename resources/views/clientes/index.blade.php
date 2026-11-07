@extends('layouts.administrador')

@section('contenido')
    <div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Administrador</p><h1>Clientes</h1></div></div>
    <div class="row g-4">
        <section class="col-lg-7" aria-labelledby="lista-clientes">
            <div class="card"><div class="card-body"><h2 id="lista-clientes" class="h5">Clientes registrados</h2>
                <form method="get" action="{{ route('clientes.index') }}" class="row g-2 align-items-end mb-3">
                    <div class="col-sm"><label for="buscar-identificacion" class="form-label">Buscar por identificación</label><input id="buscar-identificacion" class="form-control" name="numero_identificacion" maxlength="20" value="{{ request('numero_identificacion') }}"></div>
                    <div class="col-sm-auto d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Buscar</button><a class="btn btn-outline-secondary" href="{{ route('clientes.index') }}">Limpiar</a></div>
                </form>
                <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Identificación</th><th>Ficha</th></tr></thead><tbody>
                    @forelse($clientes as $cliente)
                        <tr><td>{{ $cliente->nombre }}</td><td>{{ $cliente->numero_identificacion }}</td><td><a href="{{ route('clientes.ver', $cliente->id_cliente) }}">Ver cliente y equipos</a></td></tr>
                    @empty
                        <tr><td colspan="3" class="text-body-secondary py-4">No se encontraron clientes.</td></tr>
                    @endforelse
                </tbody></table></div>
                {{ $clientes->links() }}
            </div></div>
        </section>
        <section class="col-lg-5" aria-labelledby="nuevo-cliente">
            <div class="card"><div class="card-body"><h2 id="nuevo-cliente" class="h5 mb-3">Nuevo cliente</h2>
                @if($repetido)
                    <div class="alert alert-warning">Este cliente ya existe: {{ $repetido->nombre }}. <a href="{{ route('clientes.ver', $repetido->id_cliente) }}">Ver ficha</a></div>
                @endif
                <form method="post" action="{{ route('clientes.guardar') }}">@csrf
                    <x-campo nombre="nombre" etiqueta="Nombre *" required maxlength="100" />
                    <x-campo nombre="numero_identificacion" etiqueta="Número de identificación *" required maxlength="20" />
                    <x-campo nombre="correo" etiqueta="Correo electrónico (opcional)" tipo="email" maxlength="100" />
                    <button class="btn btn-primary" type="submit">Registrar cliente</button>
                </form>
            </div></div>
        </section>
    </div>
@endsection
