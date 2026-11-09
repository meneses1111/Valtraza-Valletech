<?php

use App\Http\Controllers\Servicios\ServiciosController;
use App\Http\Controllers\Supervision\SupervisionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:administrador'])->group(function (): void {
    Route::get('/servicios', [SupervisionController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/nuevo', [ServiciosController::class, 'crear'])->name('servicios.crear');
    Route::post('/servicios', [ServiciosController::class, 'guardar'])->name('servicios.guardar');
    Route::get('/servicios/{servicio}/registrado', [ServiciosController::class, 'registrado'])->whereNumber('servicio')->name('servicios.registrado');
    Route::get('/servicios/{servicio}/asignar', [ServiciosController::class, 'asignacion'])->whereNumber('servicio')->name('servicios.asignacion');
    Route::post('/servicios/{servicio}/asignar', [ServiciosController::class, 'asignar'])->whereNumber('servicio')->name('servicios.asignar');
    Route::get('/servicios/{servicio}', [SupervisionController::class, 'ver'])->whereNumber('servicio')->name('servicios.ver');
});
