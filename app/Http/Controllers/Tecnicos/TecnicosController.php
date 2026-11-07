<?php

namespace App\Http\Controllers\Tecnicos;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolicitudAdministrador;
use App\Http\Requests\Tecnicos\RegistrarTecnicoRequest;
use App\Services\Tecnicos\RegistroTecnicos;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TecnicosController extends Controller
{
    public function __construct(private RegistroTecnicos $registros) {}

    public function index(SolicitudAdministrador $request): View
    {
        return view('tecnicos.index', ['tecnicos' => $this->registros->listar()]);
    }

    public function guardar(RegistrarTecnicoRequest $request): RedirectResponse
    {
        $this->registros->registrar($request->validated());

        return redirect()->route('tecnicos.index')->with('aviso', 'Técnico registrado.')->with('confirmacion', 'Técnico registrado');
    }
}
