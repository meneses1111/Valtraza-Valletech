<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\CuentasController;
use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__.'/modulos/*.php') as $archivo) {
    require $archivo;
}

Route::middleware('guest')->group(function (): void {
    Route::get('/ingresar', [AccesoController::class, 'login'])->name('login');
    Route::post('/ingresar', [AccesoController::class, 'iniciar'])->middleware('throttle:acceso')->name('login.iniciar');
    Route::get('/activar', [AccesoController::class, 'activacion'])->name('activar');
    Route::post('/activar', [AccesoController::class, 'activar'])->middleware('throttle:acceso')->name('activar.guardar');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/panel', fn () => view('acceso.panel'))->name('panel');
    Route::post('/salir', [AccesoController::class, 'salir'])->name('salir');
    Route::middleware('rol:administrador')->group(function (): void {
        Route::get('/cuentas/nueva', [CuentasController::class, 'crear'])->name('cuentas.crear');
        Route::post('/cuentas', [CuentasController::class, 'guardar'])->name('cuentas.guardar');
    });
});
