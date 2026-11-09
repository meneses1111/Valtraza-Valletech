<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Servicios\AsignarTecnicoRequest;
use App\Http\Requests\Servicios\RegistrarServicioRequest;
use App\Http\Requests\Servicios\SeleccionarClienteRequest;
use App\Http\Requests\SolicitudAdministrador;
use App\Services\ClientesEquipos\RegistroClientesEquipos;
use App\Services\Servicios\GestionServicios;
use App\Services\Tecnicos\RegistroTecnicos;
use App\Support\EntradaFormulario;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiciosController extends Controller
{
    public function __construct(private GestionServicios $servicios) {}

    public function crear(SeleccionarClienteRequest $request, RegistroClientesEquipos $clientes): View
    {
        $idCliente = $request->validated('id_cliente') ?? EntradaFormulario::identificador(old('id_cliente'));

        return view('servicios.crear', [
            'clientes' => $clientes->listar(),
            'cliente' => $idCliente ? $clientes->obtener((int) $idCliente) : null,
        ]);
    }

    public function guardar(RegistrarServicioRequest $request): RedirectResponse
    {
        $servicio = $this->servicios->registrar($request->validated());

        return redirect()->route('servicios.registrado', $servicio->id_servicio);
    }

    public function registrado(SolicitudAdministrador $request, int $servicio): View
    {
        return view('servicios.registrado', ['servicio' => $this->servicios->obtener($servicio)]);
    }

    public function asignacion(SolicitudAdministrador $request, int $servicio, RegistroTecnicos $tecnicos): View
    {
        return view('servicios.asignar', ['servicio' => $this->servicios->obtener($servicio), 'tecnicos' => $tecnicos->listar()]);
    }

    public function asignar(AsignarTecnicoRequest $request, int $servicio): RedirectResponse
    {
        $this->servicios->asignar($servicio, (int) $request->validated('id_tecnico'));

        return redirect()->route('servicios.ver', $servicio)->with('aviso', 'Técnico asignado. Se conservaron el estado y los registros del servicio.');
    }
}
