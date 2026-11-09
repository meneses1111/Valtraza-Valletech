<?php

use App\Http\Controllers\Tecnicos\TecnicosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:administrador'])->group(function (): void {
    Route::get('/tecnicos', [TecnicosController::class, 'index'])->name('tecnicos.index');
    Route::post('/tecnicos', [TecnicosController::class, 'guardar'])->name('tecnicos.guardar');
});
