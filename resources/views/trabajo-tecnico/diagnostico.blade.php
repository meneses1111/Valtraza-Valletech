@extends('trabajo-tecnico.formulario')

@section('titulo_formulario', 'Registrar diagnóstico')

@section('formulario')
<form method="post" action="{{ route('diagnosticos.guardar', $servicio->id_servicio) }}">@csrf
    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="sin_fallas" name="sin_fallas" value="1" @checked(old('sin_fallas')) data-sin-fallas><label class="form-check-label" for="sin_fallas">No se encontraron fallas</label></div>
    <x-campo nombre="resultado" etiqueta="Resultado del diagnóstico / fallas encontradas *" tipo="textarea" />
    <x-campo nombre="pruebas_realizadas" etiqueta="Pruebas realizadas (opcional)" tipo="textarea" />
    <x-campo nombre="observaciones" etiqueta="Observaciones (opcional)" tipo="textarea" />
    <button class="btn btn-primary" type="submit">Guardar diagnóstico</button>
</form>
@endsection
