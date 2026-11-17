<?php

namespace App\Http\Controllers\Supervision;

use App\Http\Controllers\Controller;
use App\Http\Requests\Servicios\FiltrarServiciosRequest;
use App\Http\Requests\SolicitudAdministrador;
use App\Services\Consulta\SupervisionServicios;
use App\Services\Tecnicos\RegistroTecnicos;
use Illuminate\View\View;

class SupervisionController extends Controller
{
    public function index(FiltrarServiciosRequest $request, SupervisionServicios $supervision, RegistroTecnicos $tecnicos): View
    {
        return view('servicios.index', [
            'servicios' => $supervision->listar($request->user(), $request->validated()),
            'tecnicos' => $tecnicos->listar(),
            'tecnicoElegido' => $request->validated('id_tecnico') ? $tecnicos->obtener((int) $request->validated('id_tecnico')) : null,
        ]);
    }

    public function ver(SolicitudAdministrador $request, int $servicio, SupervisionServicios $supervision): View
    {
        return view('servicios.ver', $supervision->detalle($request->user(), $servicio));
    }
}
