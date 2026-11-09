@extends('trabajo-tecnico.formulario')

@section('titulo_formulario', 'Registrar mantenimiento o reparación')

@section('formulario')
<form method="post" action="{{ route('trabajos.guardar', $servicio->id_servicio) }}">@csrf
    <div class="mb-3"><label class="form-label" for="tipo">Tipo de actividad *</label><select id="tipo" name="tipo" @class(['form-select', 'is-invalid' => $errors->has('tipo')]) required><option value="">Selecciona una actividad</option>@foreach(\App\Enums\TipoTrabajo::cases() as $tipo)<option value="{{ $tipo->value }}" @selected(old('tipo') === $tipo->value)>{{ $tipo->etiqueta() }}</option>@endforeach</select>@error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <x-campo nombre="descripcion" etiqueta="Descripción del trabajo *" tipo="textarea" required />
    <x-campo nombre="observaciones" etiqueta="Observaciones (opcional)" tipo="textarea" />
    <button class="btn btn-primary" type="submit">Guardar actividad</button>
</form>
@endsection
