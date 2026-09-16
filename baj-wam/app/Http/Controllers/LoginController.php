<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function autenticar(Request $request)
    {
        $request->merge([
            'usuario' => trim(
                (string) $request->input('usuario')
            ),
        ]);

        $datos = $request->validate(
            [
                'usuario' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'usuario.required' =>
                    'Ingresa tu usuario o correo electrónico.',

                'usuario.max' =>
                    'El usuario o correo electrónico no es válido.',

                'password.required' =>
                    'Ingresa tu contraseña.',
            ]
        );

        try {
            $identificador = $datos['usuario'];

            /*
             * La combinación usuario/correo + IP identifica
             * los intentos de inicio de sesión.
             */
            $claveLimite = mb_strtolower($identificador)
                . '|'
                . $request->ip();

            /*
             * Máximo 5 intentos fallidos.
             */
            if (RateLimiter::tooManyAttempts($claveLimite, 5)) {

                $segundos = RateLimiter::availableIn($claveLimite);
                $minutos = (int) ceil($segundos / 60);

                return back()
                    ->withErrors([
                        'usuario' =>
                            "Demasiados intentos fallidos. Inténtalo nuevamente en {$minutos} minuto(s).",
                    ])
                    ->onlyInput('usuario');
            }

            $campo = filter_var(
                $identificador,
                FILTER_VALIDATE_EMAIL
            )
                ? 'correo'
                : 'usuario';

            $usuario = Usuario::where(
                $campo,
                $identificador
            )->first();

            /*
             * Usuario inexistente o contraseña incorrecta.
             */
            if (
                !$usuario ||
                !Hash::check($datos['password'], $usuario->password)
            ) {
                RateLimiter::hit(
                    $claveLimite,
                    300
                );

                return back()
                    ->withErrors([
                        'usuario' =>
                            'El usuario, correo o contraseña son incorrectos.',
                    ])
                    ->onlyInput('usuario');
            }

            /*
             * La contraseña es correcta.
             * Ahora podemos informar que la cuenta está desactivada.
             */
            if (!$usuario->activo) {
                return back()
                    ->withErrors([
                        'usuario' =>
                            'Tu cuenta se encuentra desactivada.',
                    ])
                    ->onlyInput('usuario');
            }

            /*
             * Login correcto: eliminamos los intentos fallidos.
             */
            RateLimiter::clear($claveLimite);

            /* $recordarme = $request->boolean('remember'); */

            Auth::login($usuario);

            $request->session()->regenerate();

            // CAMBIO OBLIGATORIO DE CONTRASEÑA

            if ($usuario->debe_cambiar_password) {
                return redirect()
                    ->route('password.cambiar');
            }

            return redirect()
                ->intended(route('dashboard'));

        } catch (Throwable $e) {

            Log::error(
                'Error durante el inicio de sesión.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return back()
                ->onlyInput('usuario')
                ->with(
                    'error',
                    'No fue posible iniciar sesión. Inténtalo nuevamente.'
                );
        }
    }

    public function cerrarSesion(Request $request)
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'La sesión fue cerrada correctamente.'
                );

        } catch (Throwable $e) {
            Log::error(
                'Error al cerrar sesión.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'No fue posible cerrar la sesión.'
                );
        }
    }
}