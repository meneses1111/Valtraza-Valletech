<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearCuentaRequest;
use App\Services\Cuentas\Acceso;
use App\Services\Tecnicos\RegistroTecnicos;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CuentasController extends Controller
{
    public function crear(RegistroTecnicos $tecnicos, Acceso $acceso): View
    {
        return view('acceso.crear-cuenta', ['tecnicos' => $tecnicos->listar(), 'cuentas' => $acceso->listarCuentas()]);
    }

    public function guardar(CrearCuentaRequest $request, Acceso $acceso): RedirectResponse
    {
        $codigo = $acceso->crearCuentaTecnico($request->validated());

        return redirect()->route('cuentas.crear')->with('codigo_activacion', $codigo);
    }
}
