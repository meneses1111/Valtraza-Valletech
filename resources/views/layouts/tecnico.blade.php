@extends('layouts.base')

@section('navegacion')
    @if(\Illuminate\Support\Facades\Route::has('mis-servicios.index'))<a class="nav-link" href="{{ route('mis-servicios.index') }}">Mis servicios</a>@endif
@endsection
