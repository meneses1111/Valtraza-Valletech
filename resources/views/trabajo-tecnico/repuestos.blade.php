@extends('trabajo-tecnico.formulario')

@section('titulo_formulario', 'Registrar repuestos utilizados')

@section('formulario')
<form method="post" action="{{ route('repuestos.guardar', $servicio->id_servicio) }}">@csrf
    <p class="text-body-secondary">Agrega los repuestos utilizados en este servicio. Las filas se guardan juntas al confirmar.</p>
    @php($filas = is_array(old('repuestos')) ? old('repuestos') : [['nombre' => '', 'cantidad' => 1, 'observacion' => '']])
    <div data-lista-repuestos>
    @foreach($filas as $indice => $fila)
        <fieldset class="border rounded p-3 mb-3" data-fila-repuesto data-indice="{{ $indice }}"><legend class="float-none w-auto px-2 h6">Repuesto</legend>
            <div class="row g-3">
                <div class="col-sm-8"><label class="form-label" for="repuesto-nombre-{{ $indice }}">Nombre *</label><input class="form-control" id="repuesto-nombre-{{ $indice }}" name="repuestos[{{ $indice }}][nombre]" value="{{ \App\Support\EntradaFormulario::texto(data_get($fila, 'nombre')) }}" maxlength="100" required>@error("repuestos.$indice.nombre")<p class="text-danger small">{{ $message }}</p>@enderror</div>
                <div class="col-sm-4"><label class="form-label" for="repuesto-cantidad-{{ $indice }}">Cantidad *</label><input class="form-control" id="repuesto-cantidad-{{ $indice }}" name="repuestos[{{ $indice }}][cantidad]" type="number" min="1" max="2147483647" step="1" value="{{ \App\Support\EntradaFormulario::texto(data_get($fila, 'cantidad')) }}" required>@error("repuestos.$indice.cantidad")<p class="text-danger small">{{ $message }}</p>@enderror</div>
                <div class="col-12"><label class="form-label" for="repuesto-observacion-{{ $indice }}">Observaciones (opcional)</label><textarea class="form-control" id="repuesto-observacion-{{ $indice }}" name="repuestos[{{ $indice }}][observacion]" maxlength="255" rows="2">{{ \App\Support\EntradaFormulario::texto(data_get($fila, 'observacion')) }}</textarea>@error("repuestos.$indice.observacion")<p class="text-danger small">{{ $message }}</p>@enderror</div>
            </div>
            <button class="btn btn-outline-danger btn-sm mt-3" type="button" data-quitar-repuesto>Quitar del formulario</button>
        </fieldset>
    @endforeach
    </div>
    <div class="d-flex flex-wrap gap-2"><button class="btn btn-outline-primary" type="button" data-agregar-repuesto>Agregar otro repuesto</button><button class="btn btn-primary" type="submit">Guardar repuestos</button></div>
</form>
<template id="plantilla-repuesto">
    <fieldset class="border rounded p-3 mb-3" data-fila-repuesto data-indice="__INDICE__"><legend class="float-none w-auto px-2 h6">Repuesto</legend><div class="row g-3">
        <div class="col-sm-8"><label class="form-label" for="repuesto-nombre-__INDICE__">Nombre *</label><input class="form-control" id="repuesto-nombre-__INDICE__" name="repuestos[__INDICE__][nombre]" maxlength="100" required></div>
        <div class="col-sm-4"><label class="form-label" for="repuesto-cantidad-__INDICE__">Cantidad *</label><input class="form-control" id="repuesto-cantidad-__INDICE__" name="repuestos[__INDICE__][cantidad]" type="number" min="1" max="2147483647" step="1" value="1" required></div>
        <div class="col-12"><label class="form-label" for="repuesto-observacion-__INDICE__">Observaciones (opcional)</label><textarea class="form-control" id="repuesto-observacion-__INDICE__" name="repuestos[__INDICE__][observacion]" maxlength="255" rows="2"></textarea></div>
    </div><button class="btn btn-outline-danger btn-sm mt-3" type="button" data-quitar-repuesto>Quitar del formulario</button></fieldset>
</template>
@endsection
