@extends('layouts.administrador')

@section('contenido')
<div class="cabecera-pagina"><div><p class="text-body-secondary mb-1">Administrador</p><h1>Cuentas de acceso</h1></div></div>
@if(session('codigo_activacion'))<div class="alert alert-success" role="status">Cuenta creada. Entrega este código al técnico: <strong class="codigo-seguimiento">{{ session('codigo_activacion') }}</strong></div>@endif
<div class="row g-4">
    <section class="col-lg-7" aria-labelledby="cuentas-registradas"><div class="card"><div class="card-body">
        <h2 class="h5" id="cuentas-registradas">Cuentas registradas</h2>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Usuario</th><th>Rol</th><th>Técnico</th><th>Estado</th></tr></thead><tbody>
            @forelse($cuentas as $cuenta)<tr><td>{{ $cuenta->nombre_usuario }}</td><td>{{ $cuenta->rol === 'administrador' ? 'Administrador' : 'Técnico' }}</td><td>{{ $cuenta->tecnico?->nombre ?: 'No corresponde' }}</td><td><span @class(['badge', 'text-bg-success' => $cuenta->activado, 'text-bg-secondary' => ! $cuenta->activado])>{{ $cuenta->activado ? 'Activa' : 'Pendiente de activar' }}</span></td></tr>@empty<tr><td colspan="4">Todavía no hay cuentas registradas.</td></tr>@endforelse
        </tbody></table></div>
        {{ $cuentas->links() }}
    </div></div></section>
    <section class="col-lg-5" aria-labelledby="cuenta-nueva"><div class="card"><div class="card-body">
        <h2 class="h5" id="cuenta-nueva">Crear cuenta de técnico</h2><p class="text-body-secondary small">La cuenta tendrá permisos de técnico. Cada técnico puede tener una sola cuenta.</p>
        <form method="post" action="{{ route('cuentas.guardar') }}">@csrf
            <div class="mb-3"><label class="form-label" for="id_tecnico">Técnico registrado *</label><select class="form-select" id="id_tecnico" name="id_tecnico" required><option value="">Selecciona un técnico</option>@foreach($tecnicos as $tecnico)<option value="{{ $tecnico->id_tecnico }}" @selected(\App\Support\EntradaFormulario::texto(old('id_tecnico')) === (string) $tecnico->id_tecnico)>{{ $tecnico->nombre }}</option>@endforeach</select>@error('id_tecnico')<p class="text-danger small">{{ $message }}</p>@enderror</div>
            <x-campo nombre="nombre_usuario" etiqueta="Nombre de usuario *" maxlength="50" autocomplete="off" required />
            <button class="btn btn-primary" type="submit" @disabled($tecnicos->isEmpty())>Crear cuenta</button>
        </form>
        {{ $tecnicos->links() }}
    </div></div></section>
</div>
@endsection
