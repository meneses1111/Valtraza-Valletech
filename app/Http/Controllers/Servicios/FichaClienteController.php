<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolicitudAdministrador;
use App\Services\ClientesEquipos\RegistroClientesEquipos;
use App\Services\Servicios\HistorialEquipos;
use Illuminate\View\View;

class FichaClienteController extends Controller
{
    public function ver(SolicitudAdministrador $request, int $cliente, RegistroClientesEquipos $clientes, HistorialEquipos $historial): View
    {
        $registro = $clientes->obtener($cliente);
        $equipos = $clientes->equipos($cliente);
        $registro->setRelation('equipos', $equipos->getCollection());
        $servicios = $historial->consultar($equipos->getCollection());

        return view('clientes.ficha', ['cliente' => $registro, 'equipos' => $equipos, 'historial' => $servicios, 'historialAgrupado' => $servicios->getCollection()->groupBy('id_equipo')]);
    }
}
