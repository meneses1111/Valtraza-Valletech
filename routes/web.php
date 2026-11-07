<?php

use App\Http\Controllers\AccesoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/ingresar', [AccesoController::class, 'login'])->name('login');
    Route::post('/ingresar', [AccesoController::class, 'iniciar'])->name('login.iniciar');
    Route::get('/activar', [AccesoController::class, 'activacion'])->name('activar');
    Route::post('/activar', [AccesoController::class, 'activar'])->name('activar.guardar');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/panel', fn () => view('acceso.panel'))->name('panel');
    Route::post('/salir', [AccesoController::class, 'salir'])->name('salir');
});
