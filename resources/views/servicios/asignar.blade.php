@extends('layouts.administrador')

@section('contenido')
    <a class="d-inline-block mb-3" href="{{ route('servicios.ver', $servicio->id_servicio) }}">Volver al servicio</a>
    <h1 class="h3 mb-4">Asignar o cambiar técnico</h1>
    <x-resumen-servicio :servicio="$servicio" />
    <div class="card formulario-centrado"><div class="card-body">
        <p class="alert alert-info">Al cambiar de técnico se conservan el estado, los diagnósticos, las actividades y los repuestos registrados.</p>
        <form method="post" action="{{ route('servicios.asignar', $servicio->id_servicio) }}">@csrf
            <div class="mb-3"><label class="form-label" for="id_tecnico">Técnico encargado *</label><select id="id_tecnico" name="id_tecnico" @class(['form-select', 'is-invalid' => $errors->has('id_tecnico')]) required><option value="">Selecciona un técnico</option>
                @foreach($tecnicos as $tecnico)<option value="{{ $tecnico->id_tecnico }}" @selected((string) old('id_tecnico', $servicio->id_tecnico) === (string) $tecnico->id_tecnico)>{{ $tecnico->nombre }}</option>@endforeach
            </select>@error('id_tecnico')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            @if($tecnicos->isEmpty())<p class="alert alert-warning">Primero registra un técnico desde el menú Técnicos.</p>@endif
            <button class="btn btn-primary" type="submit" @disabled($tecnicos->isEmpty())>Guardar asignación</button>
        </form>
    </div></div>
@endsection
