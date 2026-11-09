<?php

namespace App\Http\Controllers\ClientesEquipos;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientesEquipos\BuscarClientesRequest;
use App\Http\Requests\ClientesEquipos\RegistrarClienteRequest;
use App\Services\ClientesEquipos\RegistroClientesEquipos;
use App\Support\EntradaFormulario;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientesController extends Controller
{
    public function __construct(private RegistroClientesEquipos $registros) {}

    public function index(BuscarClientesRequest $request): View
    {
        return view('clientes.index', [
            'clientes' => $this->registros->buscar($request->validated('numero_identificacion')),
            'repetido' => $this->registros->buscarPorIdentificacion(EntradaFormulario::texto(old('numero_identificacion'))),
        ]);
    }

    public function guardar(RegistrarClienteRequest $request): RedirectResponse
    {
        $cliente = $this->registros->registrarCliente($request->validated());

        return redirect()->route('clientes.ver', $cliente->id_cliente)->with('aviso', 'Cliente registrado.')->with('confirmacion', 'Cliente registrado');
    }
}
