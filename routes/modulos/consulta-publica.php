<?php

use App\Http\Controllers\Consulta\ConsultaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsultaController::class, 'inicio'])->name('inicio');
Route::post('/consultar', [ConsultaController::class, 'consultar'])->middleware('throttle:consulta-publica')->name('consulta.consultar');
