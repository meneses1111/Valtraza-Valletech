@props(['nombre', 'etiqueta', 'tipo' => 'text', 'valor' => ''])

<div class="mb-3">
    <label class="form-label" for="{{ $nombre }}">{{ $etiqueta }}</label>
    @if ($tipo === 'textarea')
        <textarea id="{{ $nombre }}" name="{{ $nombre }}" rows="4" {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }} @if($errors->has($nombre)) aria-invalid="true" aria-describedby="{{ $nombre }}-error" @endif>{{ \App\Support\EntradaFormulario::texto(old($nombre, $valor)) }}</textarea>
    @else
        <input id="{{ $nombre }}" name="{{ $nombre }}" type="{{ $tipo }}" value="{{ $tipo === 'password' ? '' : \App\Support\EntradaFormulario::texto(old($nombre, $valor)) }}" {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }} @if($errors->has($nombre)) aria-invalid="true" aria-describedby="{{ $nombre }}-error" @endif>
    @endif
    @error($nombre)<div id="{{ $nombre }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
</div>
