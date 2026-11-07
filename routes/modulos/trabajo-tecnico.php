<?php

use App\Http\Controllers\TrabajoTecnico\MisServiciosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:tecnico'])->group(function (): void {
    Route::get('/mis-servicios', [MisServiciosController::class, 'index'])->name('mis-servicios.index');
    Route::get('/mis-servicios/{servicio}', [MisServiciosController::class, 'ver'])->whereNumber('servicio')->name('mis-servicios.ver');
});
