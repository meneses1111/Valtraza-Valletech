<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivarCuentaRequest;
use App\Http\Requests\IniciarSesionRequest;
use App\Models\Usuario;
use App\Services\Cuentas\Acceso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function login(): View
    {
        return view('acceso.login');
    }

    public function iniciar(IniciarSesionRequest $request, Acceso $acceso): RedirectResponse
    {
        $datos = $request->validated();
        $clave = 'login:'.Str::lower($datos['nombre_usuario']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            abort(429, '', ['Retry-After' => (string) RateLimiter::availableIn($clave)]);
        }

        $usuario = Usuario::query()->where('nombre_usuario', $datos['nombre_usuario'])->first();

        if (! $usuario || ! $acceso->verificar($usuario, $datos['password'])) {
            RateLimiter::hit($clave, 60);

            return back()->withErrors(['nombre_usuario' => 'No se pudo iniciar sesión con estos datos.']);
        }

        RateLimiter::clear($clave);
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('panel');
    }

    public function activacion(): View
    {
        return view('acceso.activar');
    }

    public function activar(ActivarCuentaRequest $request, Acceso $acceso): RedirectResponse
    {
        $datos = $request->validated();
        $clave = 'activar:'.$request->ip();

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            abort(429, '', ['Retry-After' => (string) RateLimiter::availableIn($clave)]);
        }

        if (! $acceso->activar($datos['codigo_activacion'], $datos['password'])) {
            RateLimiter::hit($clave, 60);

            return back()->withErrors(['codigo_activacion' => 'Código inválido o ya utilizado.']);
        }

        RateLimiter::clear($clave);

        return redirect()->route('login')->with('aviso', 'Cuenta activada. Ya puedes ingresar.');
    }

    public function salir(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
