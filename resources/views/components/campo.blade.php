@props(['nombre', 'etiqueta', 'tipo' => 'text', 'valor' => ''])

@php
    $describe = trim(($attributes->get('aria-describedby') ?? '').($errors->has($nombre) ? ' '.$nombre.'-error' : ''));
    $atributos = $attributes->except('aria-describedby');
@endphp
<div class="mb-3">
    <label class="form-label" for="{{ $nombre }}">{{ $etiqueta }}</label>
    @if ($tipo === 'textarea')
        <textarea id="{{ $nombre }}" name="{{ $nombre }}" rows="4" {{ $atributos->merge(['maxlength' => \App\Support\EntradaFormulario::MAX_TEXTO])->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }} @if($errors->has($nombre)) aria-invalid="true" @endif @if($describe !== '') aria-describedby="{{ $describe }}" @endif>{{ \App\Support\EntradaFormulario::texto(old($nombre, $valor)) }}</textarea>
    @else
        <input id="{{ $nombre }}" name="{{ $nombre }}" type="{{ $tipo }}" value="{{ $tipo === 'password' ? '' : \App\Support\EntradaFormulario::texto(old($nombre, $valor)) }}" {{ $atributos->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }} @if($errors->has($nombre)) aria-invalid="true" @endif @if($describe !== '') aria-describedby="{{ $describe }}" @endif>
    @endif
    @error($nombre)<div id="{{ $nombre }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
    {{ $slot }}
</div>
