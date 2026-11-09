<?php

namespace App\Http\Controllers\ClientesEquipos;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientesEquipos\RegistrarEquipoRequest;
use App\Services\ClientesEquipos\RegistroClientesEquipos;
use Illuminate\Http\RedirectResponse;

class EquiposController extends Controller
{
    public function guardar(RegistrarEquipoRequest $request, int $cliente, RegistroClientesEquipos $registros): RedirectResponse
    {
        $registros->registrarEquipo($cliente, $request->validated());

        return redirect()->route('clientes.ver', $cliente)->with('aviso', 'Equipo registrado.')->with('confirmacion', 'Equipo registrado');
    }
}
