@extends('layouts.base')

@section('contenido')
    <h1>Activar cuenta</h1>
    <form method="post" action="{{ route('activar.guardar') }}">
        @csrf
        <label for="codigo_activacion">Código de activación</label>
        <input id="codigo_activacion" name="codigo_activacion" maxlength="64" required>
        @error('codigo_activacion') <p role="alert">{{ $message }}</p> @enderror
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" minlength="8" required>
        @error('password') <p role="alert">{{ $message }}</p> @enderror
        <label for="password_confirmation">Confirmar contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
        <button type="submit">Activar</button>
    </form>
@endsection
