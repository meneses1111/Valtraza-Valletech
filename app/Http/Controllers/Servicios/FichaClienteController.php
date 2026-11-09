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

        return view('clientes.ficha', ['cliente' => $registro, 'historial' => $historial->consultar($registro->equipos)]);
    }
}
