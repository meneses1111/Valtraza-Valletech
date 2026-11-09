<?php

namespace App\Http\Controllers\Consulta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consulta\ConsultarServicioRequest;
use App\Services\Consulta\ConsultaPublica;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function inicio(): View
    {
        return view('consulta.inicio');
    }

    public function consultar(ConsultarServicioRequest $request, ConsultaPublica $consulta): View|RedirectResponse
    {
        $resultado = $consulta->consultar($request->validated('codigo_seguimiento'));

        if (! $resultado) {
            return redirect()->route('inicio')
                ->withErrors(['codigo_seguimiento' => 'No encontramos un servicio con ese código.']);
        }

        return view('consulta.resultado', ['resultado' => $resultado]);
    }
}
