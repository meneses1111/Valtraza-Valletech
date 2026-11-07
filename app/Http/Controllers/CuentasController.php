<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearCuentaRequest;
use App\Models\Tecnico;
use App\Services\Cuentas\Acceso;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CuentasController extends Controller
{
    public function crear(): View
    {
        return view('acceso.crear-cuenta', ['tecnicos' => Tecnico::query()->orderBy('nombre')->get()]);
    }

    public function guardar(CrearCuentaRequest $request, Acceso $acceso): RedirectResponse
    {
        $codigo = $acceso->crearCuentaTecnico($request->validated());

        return redirect()->route('cuentas.crear')->with('codigo_activacion', $codigo);
    }
}
