@extends('layouts.administrador')

@section('contenido')
    <h1>Crear cuenta de técnico</h1>
    @if (session('codigo_activacion'))
        <p role="status">Entrega este código al técnico: <strong>{{ session('codigo_activacion') }}</strong></p>
    @endif
    <form method="post" action="{{ route('cuentas.guardar') }}">
        @csrf
        <label for="id_tecnico">Técnico</label>
        <select id="id_tecnico" name="id_tecnico" required>
            <option value="">Selecciona un técnico</option>
            @foreach ($tecnicos as $tecnico)
                <option value="{{ $tecnico->id_tecnico }}" @selected(old('id_tecnico') == $tecnico->id_tecnico)>{{ $tecnico->nombre }}</option>
            @endforeach
        </select>
        @error('id_tecnico') <p role="alert">{{ $message }}</p> @enderror
        <label for="nombre_usuario">Nombre de usuario</label>
        <input id="nombre_usuario" name="nombre_usuario" maxlength="50" value="{{ old('nombre_usuario') }}" required>
        @error('nombre_usuario') <p role="alert">{{ $message }}</p> @enderror
        <button type="submit">Crear cuenta</button>
    </form>
@endsection
