@extends(auth()->user()->rol === 'administrador' ? 'layouts.administrador' : 'layouts.tecnico')

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->nombre_usuario }}</h1>
    @if (auth()->user()->rol === 'administrador')
        <a href="{{ route('cuentas.crear') }}">Crear cuenta de técnico</a>
    @endif
    @if(auth()->user()->rol === 'administrador' && \Illuminate\Support\Facades\Route::has('servicios.crear'))
        <div class="d-flex flex-wrap gap-3 mt-4"><a class="btn btn-primary" href="{{ route('servicios.crear') }}">Nuevo servicio</a><a class="btn btn-outline-primary" href="{{ route('servicios.index') }}">Ver servicios</a></div>
    @elseif(auth()->user()->rol === 'tecnico' && \Illuminate\Support\Facades\Route::has('mis-servicios.index'))
        <a class="btn btn-primary" href="{{ route('mis-servicios.index') }}">Ver mis servicios</a>
    @endif
@endsection
