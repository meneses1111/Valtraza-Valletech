@extends('trabajo-tecnico.formulario')

@section('titulo_formulario', 'Cambiar estado')

@section('formulario')
<p class="alert alert-info">Para completar el servicio debes registrar al menos un diagnóstico. <a href="{{ route('diagnosticos.crear', $servicio->id_servicio) }}">Registrar diagnóstico</a></p>
<form method="post" action="{{ route('estado.guardar', $servicio->id_servicio) }}">@csrf
    <div class="mb-3"><label class="form-label" for="estado">Estado del servicio *</label><select id="estado" name="estado" @class(['form-select', 'is-invalid' => $errors->has('estado')]) required><option value="">Selecciona un estado</option>@foreach(\App\Enums\EstadoServicio::cases() as $estado)<option value="{{ $estado->value }}" @selected(old('estado', $servicio->estado) === $estado->value)>{{ $estado->etiqueta() }}</option>@endforeach</select>@error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <button class="btn btn-primary" type="submit">Guardar estado</button>
</form>
@endsection
