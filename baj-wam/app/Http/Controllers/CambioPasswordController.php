<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Throwable;

class CambioPasswordController extends Controller
{
    public function edit(Request $request)
    {
        // VERIFICAR CAMBIO PENDIENTE

        if (!$request->user()->debe_cambiar_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.cambiar-password');
    }

    public function update(Request $request)
    {
        $datos = $request->validate(
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(10)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ],
            [
                'password.required' =>
                    'La nueva contraseña es obligatoria.',

                'password.confirmed' =>
                    'Las contraseñas no coinciden.',
            ]
        );

        try {
            $usuario = $request->user();

            // EVITAR REUTILIZAR LA CONTRASEÑA TEMPORAL

            if (Hash::check($datos['password'], $usuario->password)) {
                return back()
                    ->withErrors([
                        'password' =>
                            'La nueva contraseña debe ser diferente a la contraseña temporal.',
                    ]);
            }

            // ACTUALIZAR CONTRASEÑA

            $usuario->update([
                'password' => Hash::make($datos['password']),
                'debe_cambiar_password' => false,
                'fecha_ultimo_cambio_password' => now(),
            ]);

            // REGENERAR SESIÓN

            $request->session()->regenerate();

            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'La contraseña fue actualizada correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al cambiar la contraseña obligatoria.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_usuario' => $request->user()?->id_usuario,
                ]
            );

            return back()->with(
                'error',
                'No fue posible actualizar la contraseña.'
            );
        }
    }
}