@extends('layouts.administrador')

@section('contenido')
    <div class="cabecera-pagina"><div><a href="{{ route('servicios.index') }}">Volver a servicios</a><h1 class="mt-2">Nuevo servicio</h1></div></div>
    <div class="card formulario-centrado"><div class="card-body">
        <form method="get" action="{{ route('servicios.crear') }}" class="mb-4">
            <label for="seleccionar-cliente" class="form-label">Cliente *</label>
            <div class="d-flex flex-wrap gap-2"><select id="seleccionar-cliente" class="form-select selector-cliente" name="id_cliente" required><option value="">Selecciona un cliente</option>
                @foreach($clientes as $opcion)<option value="{{ $opcion->id_cliente }}" @selected($cliente?->id_cliente === $opcion->id_cliente)>{{ $opcion->nombre }} · {{ $opcion->numero_identificacion }}</option>@endforeach
            </select><button class="btn btn-outline-primary" type="submit">Ver computadores</button></div>
            <a class="small d-inline-block mt-2" href="{{ route('clientes.index') }}">Registrar un cliente nuevo</a>
        </form>
        @if($cliente)
            <form method="post" action="{{ route('servicios.guardar') }}">@csrf
                <input type="hidden" name="id_cliente" value="{{ $cliente->id_cliente }}">
                <div class="mb-3"><label for="id_equipo" class="form-label">Computador de {{ $cliente->nombre }} *</label><select id="id_equipo" name="id_equipo" @class(['form-select', 'is-invalid' => $errors->has('id_equipo')]) required><option value="">Selecciona un computador</option>
                    @foreach($cliente->equipos as $equipo)<option value="{{ $equipo->id_equipo }}" @selected(\App\Support\EntradaFormulario::texto(old('id_equipo', request('id_equipo'))) === (string) $equipo->id_equipo)>{{ $equipo->codigo_equipo }} · {{ $equipo->modelo ?: 'Modelo no registrado' }}</option>@endforeach
                </select>@error('id_equipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <a class="small d-inline-block mt-2" href="{{ route('clientes.ver', $cliente->id_cliente) }}">Registrar un computador nuevo</a>
                </div>
                <x-campo nombre="fecha" etiqueta="Fecha del servicio *" tipo="date" :valor="now()->timezone('America/Bogota')->toDateString()" required />
                <x-campo nombre="nombre" etiqueta="Nombre del servicio *" required maxlength="100" />
                <x-campo nombre="descripcion_problema" etiqueta="Descripción del problema *" tipo="textarea" required />
                <button class="btn btn-primary" type="submit" @disabled($cliente->equipos->isEmpty())>Registrar servicio</button>
            </form>
        @else
            <p class="alert alert-info mb-0">Selecciona el cliente para elegir uno de sus computadores.</p>
        @endif
    </div></div>
@endsection
