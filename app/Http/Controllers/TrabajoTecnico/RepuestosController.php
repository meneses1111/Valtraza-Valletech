<?php

namespace App\Http\Controllers\TrabajoTecnico;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrabajoTecnico\RegistrarRepuestosRequest;
use App\Http\Requests\TrabajoTecnico\SolicitudTecnico;
use App\Services\Servicios\GestionServicios;
use App\Services\TrabajoTecnico\RegistroTrabajoTecnico;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RepuestosController extends Controller
{
    public function crear(SolicitudTecnico $request, int $servicio, GestionServicios $servicios): View
    {
        return view('trabajo-tecnico.repuestos', ['servicio' => $servicios->asignado($servicio, $request->user())]);
    }

    public function guardar(RegistrarRepuestosRequest $request, int $servicio, RegistroTrabajoTecnico $registros): RedirectResponse
    {
        $registros->registrarRepuestos($servicio, $request->user(), $request->validated());

        return redirect()->route('mis-servicios.ver', $servicio)->with('aviso', 'Repuestos registrados.');
    }
}
