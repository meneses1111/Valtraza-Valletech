<?php

use App\Http\Controllers\TrabajoTecnico\DiagnosticosController;
use App\Http\Controllers\TrabajoTecnico\EstadosController;
use App\Http\Controllers\TrabajoTecnico\MisServiciosController;
use App\Http\Controllers\TrabajoTecnico\RepuestosController;
use App\Http\Controllers\TrabajoTecnico\TrabajosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:tecnico'])->group(function (): void {
    Route::get('/mis-servicios', [MisServiciosController::class, 'index'])->name('mis-servicios.index');
    Route::get('/mis-servicios/{servicio}', [MisServiciosController::class, 'ver'])->whereNumber('servicio')->name('mis-servicios.ver');
    Route::get('/mis-servicios/{servicio}/diagnosticos', [DiagnosticosController::class, 'crear'])->whereNumber('servicio')->name('diagnosticos.crear');
    Route::post('/mis-servicios/{servicio}/diagnosticos', [DiagnosticosController::class, 'guardar'])->whereNumber('servicio')->name('diagnosticos.guardar');
    Route::get('/mis-servicios/{servicio}/trabajos', [TrabajosController::class, 'crear'])->whereNumber('servicio')->name('trabajos.crear');
    Route::post('/mis-servicios/{servicio}/trabajos', [TrabajosController::class, 'guardar'])->whereNumber('servicio')->name('trabajos.guardar');
    Route::get('/mis-servicios/{servicio}/repuestos', [RepuestosController::class, 'crear'])->whereNumber('servicio')->name('repuestos.crear');
    Route::post('/mis-servicios/{servicio}/repuestos', [RepuestosController::class, 'guardar'])->whereNumber('servicio')->name('repuestos.guardar');
    Route::get('/mis-servicios/{servicio}/estado', [EstadosController::class, 'crear'])->whereNumber('servicio')->name('estado.crear');
    Route::post('/mis-servicios/{servicio}/estado', [EstadosController::class, 'guardar'])->whereNumber('servicio')->name('estado.guardar');
});
