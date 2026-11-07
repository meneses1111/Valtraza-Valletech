<?php

use App\Http\Controllers\TrabajoTecnico\DiagnosticosController;
use App\Http\Controllers\TrabajoTecnico\MisServiciosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:tecnico'])->group(function (): void {
    Route::get('/mis-servicios', [MisServiciosController::class, 'index'])->name('mis-servicios.index');
    Route::get('/mis-servicios/{servicio}', [MisServiciosController::class, 'ver'])->whereNumber('servicio')->name('mis-servicios.ver');
    Route::get('/mis-servicios/{servicio}/diagnosticos', [DiagnosticosController::class, 'crear'])->whereNumber('servicio')->name('diagnosticos.crear');
    Route::post('/mis-servicios/{servicio}/diagnosticos', [DiagnosticosController::class, 'guardar'])->whereNumber('servicio')->name('diagnosticos.guardar');
});
