<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarCambioPassword
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $usuario = $request->user();

        // VERIFICAR CAMBIO OBLIGATORIO

        if (
            $usuario &&
            $usuario->debe_cambiar_password
        ) {
            return redirect()
                ->route('password.cambiar');
        }

        return $next($request);
    }
}