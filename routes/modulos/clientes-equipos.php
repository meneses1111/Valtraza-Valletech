<?php

use App\Http\Controllers\ClientesEquipos\ClientesController;
use App\Http\Controllers\Servicios\FichaClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:administrador'])->group(function (): void {
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClientesController::class, 'guardar'])->name('clientes.guardar');
    Route::get('/clientes/{cliente}', [FichaClienteController::class, 'ver'])->whereNumber('cliente')->name('clientes.ver');
});
