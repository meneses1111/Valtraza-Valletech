@extends('layouts.base')

@section('contenido')
    <h1>Ingresar</h1>
    <form method="post" action="{{ route('login.iniciar') }}">
        @csrf
        <label for="nombre_usuario">Usuario</label>
        <input id="nombre_usuario" name="nombre_usuario" maxlength="50" value="{{ old('nombre_usuario') }}" required>
        @error('nombre_usuario') <p role="alert">{{ $message }}</p> @enderror
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" required>
        @error('password') <p role="alert">{{ $message }}</p> @enderror
        <button type="submit">Ingresar</button>
    </form>
    <a href="{{ route('activar') }}">Activar cuenta</a>
@endsection
