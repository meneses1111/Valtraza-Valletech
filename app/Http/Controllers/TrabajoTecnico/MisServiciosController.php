<?php

namespace App\Http\Controllers\TrabajoTecnico;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrabajoTecnico\FiltrarAsignadosRequest;
use App\Http\Requests\TrabajoTecnico\SolicitudTecnico;
use App\Services\Servicios\GestionServicios;
use App\Services\TrabajoTecnico\RegistroTrabajoTecnico;
use Illuminate\View\View;

class MisServiciosController extends Controller
{
    public function index(FiltrarAsignadosRequest $request, GestionServicios $servicios): View
    {
        return view('trabajo-tecnico.index', ['servicios' => $servicios->asignados($request->user(), $request->validated('estado'))]);
    }

    public function ver(SolicitudTecnico $request, int $servicio, RegistroTrabajoTecnico $registros): View
    {
        return view('trabajo-tecnico.ver', $registros->detalle($servicio, $request->user()));
    }
}
