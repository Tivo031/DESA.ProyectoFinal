<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DomainException;
use Throwable;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        try {
            $consulta = Usuario::query()
                ->with('rol');

            $buscar = trim((string) $request->input('buscar', ''));

            if ($buscar !== '') {
                $consulta->where(function ($query) use ($buscar) {
                    $query->where('nombres', 'LIKE', '%' . $buscar . '%')
                        ->orWhere('apellidos', 'LIKE', '%' . $buscar . '%')
                        ->orWhere('usuario', 'LIKE', '%' . $buscar . '%')
                        ->orWhere('correo', 'LIKE', '%' . $buscar . '%')
                        ->orWhere('telefono', 'LIKE', '%' . $buscar . '%')
                        ->orWhereHas('rol', function ($queryRol) use ($buscar) {
                            $queryRol->where('nombre', 'LIKE', '%' . $buscar . '%');
                        });
                });
            }

            if ($request->filled('rol')) {
                $consulta->where('id_rol', $request->rol);
            }

            if (
                $request->input('estado') !== null &&
                $request->input('estado') !== ''
            ) {
                $consulta->where('activo', $request->estado);
            }

            $usuarios = $consulta
                ->orderBy('id_usuario')
                ->orderBy('apellidos')
                ->paginate(10)
                ->withQueryString();

            $roles = Rol::orderBy('nombre')->get();

            $resumenUsuarios = [
                'total' => Usuario::count(),
                'activos' => Usuario::where('activo', 1)->count(),
                'inactivos' => Usuario::where('activo', 0)->count(),
            ];

            return view(
                'usuarios.index',
                compact(
                    'usuarios',
                    'roles',
                    'resumenUsuarios'
                )
            );
        } catch (Throwable $e) {
            Log::error(
                'Error al cargar el listado de usuarios.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return back()
                ->with(
                    'error',
                    'No fue posible cargar el listado de usuarios.'
                );
        }
    }

    public function perfil(Request $request)
    {
        try {
            $usuario = $request->user();

            if (!$usuario) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'La sesión no es válida.'
                    );
            }

            $usuario->load('rol');

            return view(
                'perfil.show',
                compact('usuario')
            );
        } catch (Throwable $e) {
            Log::error(
                'Error al cargar el perfil del usuario.',
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
                    'No fue posible cargar el perfil.'
                );
        }
    }

    public function create()
    {
        try {
            $roles = Rol::where('activo', true)
                ->orderBy('nombre')
                ->get();

            return view(
                'usuarios.create',
                compact('roles')
            );
        } catch (Throwable $e) {
            Log::error(
                'Error al cargar el formulario de creación de usuarios.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]
            );

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No fue posible cargar el formulario de creación.'
                );
        }
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombres' => trim((string) $request->input('nombres')),
            'apellidos' => trim((string) $request->input('apellidos')),
            'correo' => strtolower(
                trim((string) $request->input('correo'))
            ),
            'telefono' => trim(
                (string) $request->input('telefono')
            ),
            'usuario' => trim(
                (string) $request->input('usuario')
            ),
        ]);

        $datos = $request->validate(
            [
                'nombres' => [
                    'required',
                    'string',
                    'max:80',
                    'regex:/^[\pL\s\'-]+$/u',
                ],

                'apellidos' => [
                    'required',
                    'string',
                    'max:80',
                    'regex:/^[\pL\s\'-]+$/u',
                ],

                'correo' => [
                    'required',
                    'email',
                    'max:120',
                    'unique:usuarios,correo',
                ],

                'telefono' => [
                    'nullable',
                    'regex:/^\d{4}-\d{4}$/',
                ],

                'usuario' => [
                    'required',
                    'string',
                    'min:4',
                    'max:50',
                    'regex:/^[A-Za-z0-9._]+$/',
                    'unique:usuarios,usuario',
                ],

                'id_rol' => [
                    'required',
                    'integer',
                    Rule::exists('roles', 'id_rol')
                        ->where('activo', true),
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::min(10)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],

                'activo' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'nombres.required' =>
                    'Los nombres son obligatorios.',

                'nombres.regex' =>
                    'Los nombres únicamente pueden contener letras, espacios, guiones y apóstrofes.',

                'apellidos.required' =>
                    'Los apellidos son obligatorios.',

                'apellidos.regex' =>
                    'Los apellidos únicamente pueden contener letras, espacios, guiones y apóstrofes.',

                'correo.required' =>
                    'El correo electrónico es obligatorio.',

                'correo.email' =>
                    'Ingresa un correo electrónico válido.',

                'correo.unique' =>
                    'Este correo electrónico ya está registrado.',

                'telefono.regex' =>
                    'El teléfono debe tener el formato 5555-5555.',

                'usuario.required' =>
                    'El nombre de usuario es obligatorio.',

                'usuario.min' =>
                    'El nombre de usuario debe tener al menos 4 caracteres.',

                'usuario.regex' =>
                    'El usuario únicamente puede contener letras, números, puntos y guiones bajos.',

                'usuario.unique' =>
                    'Este nombre de usuario ya está registrado.',

                'id_rol.required' =>
                    'Debes seleccionar un rol.',

                'id_rol.exists' =>
                    'El rol seleccionado no es válido.',

                'password.required' =>
                    'La contraseña es obligatoria.',

                'password.confirmed' =>
                    'Las contraseñas no coinciden.',
            ]
        );

        try {
            DB::transaction(function () use ($datos) {
                $rol = Rol::lockForUpdate()
                    ->findOrFail($datos['id_rol']);

                if (!$rol->activo) {
                    throw new DomainException(
                        'El rol seleccionado ya no está activo.'
                    );
                }

                Usuario::create([
                    'id_rol' => $rol->id_rol,
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'correo' => $datos['correo'],
                    'telefono' => $datos['telefono'] ?: null,
                    'usuario' => $datos['usuario'],
                    'password' => Hash::make(
                        $datos['password']
                    ),
                    'activo' => $datos['activo'],
                ]);
            });

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'success',
                    'El usuario fue creado correctamente.'
                );
        } catch (DomainException $e) {
            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->withErrors(['id_rol' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error(
                'Error al crear un usuario.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'usuario' => $datos['usuario'],
                    'correo' => $datos['correo'],
                ]
            );

            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->with(
                    'error',
                    'No fue posible crear el usuario. Inténtalo nuevamente.'
                );
        }
    }

    public function show(Usuario $usuario)
    {
        try {
            $usuario->load('rol');

            return view(
                'usuarios.show',
                compact('usuario')
            );
        } catch (Throwable $e) {
            Log::error(
                'Error al cargar el detalle del usuario.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_usuario' => $usuario->id_usuario,
                ]
            );

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No fue posible cargar el detalle del usuario.'
                );
        }
    }

    public function restablecerPassword(Request $request, Usuario $usuario)
    {
        // EVITAR RESTABLECER LA PROPIA CONTRASEÑA

        if ($request->user()->id_usuario === $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' =>
                    'No puedes restablecer tu propia contraseña desde esta opción.',
            ], 403);
        }

        try {
            $passwordTemporal = Str::password(12);

            DB::transaction(function () use ($usuario, $passwordTemporal) {

                // RESTABLECER CONTRASEÑA

                $usuario->update([
                    'password' => Hash::make($passwordTemporal),
                    'debe_cambiar_password' => true,
                    'fecha_ultimo_cambio_password' => null,
                ]);

                // CERRAR SESIONES ABIERTAS

                DB::table('sessions')
                    ->where(
                        'user_id',
                        $usuario->id_usuario
                    )
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' =>
                    'La contraseña fue restablecida correctamente.',
                'password_temporal' => $passwordTemporal,
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Error al restablecer la contraseña de un usuario.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_usuario' => $usuario->id_usuario,
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'No fue posible restablecer la contraseña.',
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            $roles = Rol::where('activo', true)
                ->orderBy('nombre')
                ->get();

            return view('usuarios.edit', compact('usuario', 'roles'));

        } catch (\Exception $e) {

            return redirect()
                ->route('usuarios.index')
                ->with('error', 'No fue posible cargar el usuario.');
        }
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->merge([
            'nombres' => trim((string) $request->input('nombres')),
            'apellidos' => trim((string) $request->input('apellidos')),
            'correo' => strtolower(
                trim((string) $request->input('correo'))
            ),
            'telefono' => trim(
                (string) $request->input('telefono')
            ),
            'usuario' => trim(
                (string) $request->input('usuario')
            ),
        ]);

        // REGLAS

        $reglas = [
            'nombres' => [
                'required',
                'string',
                'max:80',
                'regex:/^[\pL\s\'-]+$/u',
            ],

            'apellidos' => [
                'required',
                'string',
                'max:80',
                'regex:/^[\pL\s\'-]+$/u',
            ],

            'correo' => [
                'required',
                'email',
                'max:120',
                Rule::unique('usuarios', 'correo')
                    ->ignore(
                        $usuario->id_usuario,
                        'id_usuario'
                    ),
            ],

            'telefono' => [
                'nullable',
                'regex:/^\d{4}-\d{4}$/',
            ],

            'usuario' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[A-Za-z0-9._]+$/',
                Rule::unique('usuarios', 'usuario')
                    ->ignore(
                        $usuario->id_usuario,
                        'id_usuario'
                    ),
            ],

            'id_rol' => [
                'required',
                'integer',
                Rule::exists('roles', 'id_rol')
                    ->where('activo', true),
            ],
        ];

        // VALIDAR EDICIÓN DE CUENTA PROPIA

        $esCuentaPropia =
            (int) $request->user()->id_usuario === (int) $usuario->id_usuario;

        if ($esCuentaPropia && (int) $request->input('id_rol') !== (int) $usuario->id_rol) {
            return back()
                ->withInput()
                ->with('error', 'No puedes cambiar el rol de tu propia cuenta.');
        }

        // PERMISO PARA CAMBIAR ESTADO

        $puedeCambiarEstado =
            $request->user()->can('usuarios.desactivar')
            && (int) $request->user()->id_usuario !== (int) $usuario->id_usuario;

        if ($puedeCambiarEstado) {
            $reglas['activo'] = [
                'required',
                'boolean',
            ];
        }

        // VALIDACIÓN

        $datos = $request->validate(
            $reglas,
            [
                'nombres.required' =>
                    'Los nombres son obligatorios.',

                'nombres.regex' =>
                    'Los nombres únicamente pueden contener letras, espacios, guiones y apóstrofes.',

                'apellidos.required' =>
                    'Los apellidos son obligatorios.',

                'apellidos.regex' =>
                    'Los apellidos únicamente pueden contener letras, espacios, guiones y apóstrofes.',

                'correo.required' =>
                    'El correo electrónico es obligatorio.',

                'correo.email' =>
                    'Ingresa un correo electrónico válido.',

                'correo.unique' =>
                    'Este correo electrónico ya está registrado.',

                'telefono.regex' =>
                    'El teléfono debe tener el formato 5555-5555.',

                'usuario.required' =>
                    'El nombre de usuario es obligatorio.',

                'usuario.min' =>
                    'El nombre de usuario debe tener al menos 4 caracteres.',

                'usuario.regex' =>
                    'El usuario únicamente puede contener letras, números, puntos y guiones bajos.',

                'usuario.unique' =>
                    'Este nombre de usuario ya está registrado.',

                'id_rol.required' =>
                    'Debes seleccionar un rol.',

                'id_rol.exists' =>
                    'El rol seleccionado no es válido.',
                'activo.required' =>
                    'No es posible modificar el estado de tu propia cuenta. Por seguridad, esta acción debe realizarla otro usuario autorizado.',

                'activo.boolean' =>
                    'El estado del usuario no es válido.',
            ]
        );

        try {
            DB::transaction(function () use ($id, $datos, $puedeCambiarEstado, $request) {
                $rolSeleccionado = Rol::lockForUpdate()
                    ->findOrFail($datos['id_rol']);

                if (!$rolSeleccionado->activo) {
                    throw new DomainException(
                        'El rol seleccionado ya no está activo.'
                    );
                }

                [$rolAdministrador, $administradoresActivos] =
                    $this->bloquearAdministradoresActivos();

                $usuarioBloqueado = Usuario::lockForUpdate()
                    ->findOrFail($id);

                $seDesactiva = $puedeCambiarEstado
                    && !(bool) $datos['activo'];

                if (
                    $seDesactiva
                    && $request->user()->id_usuario === $usuarioBloqueado->id_usuario
                ) {
                    throw new DomainException(
                        'No puedes desactivar tu propia cuenta.'
                    );
                }

                $dejaDeSerAdministrador = $rolAdministrador
                    && $rolSeleccionado->id_rol !== $rolAdministrador->id_rol;

                if (
                    $this->esAdministradorActivo(
                        $usuarioBloqueado,
                        $rolAdministrador
                    )
                    && ($seDesactiva || $dejaDeSerAdministrador)
                    && $administradoresActivos->count() <= 1
                ) {
                    throw new DomainException(
                        'No es posible modificar al último administrador activo del sistema.'
                    );
                }

                $usuarioBloqueado->nombres = $datos['nombres'];
                $usuarioBloqueado->apellidos = $datos['apellidos'];
                $usuarioBloqueado->correo = $datos['correo'];
                $usuarioBloqueado->telefono = $datos['telefono'] ?: null;
                $usuarioBloqueado->usuario = $datos['usuario'];
                $usuarioBloqueado->id_rol = $rolSeleccionado->id_rol;

                if ($puedeCambiarEstado) {
                    $usuarioBloqueado->activo = (bool) $datos['activo'];
                }

                $usuarioBloqueado->save();
            });

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'success',
                    'El usuario fue actualizado correctamente.'
                );

        } catch (DomainException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (Throwable $e) {

            Log::error(
                'Error al actualizar un usuario.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_usuario' => $usuario->id_usuario,
                    'usuario' => $datos['usuario'],
                    'correo' => $datos['correo'],
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible actualizar el usuario. Inténtalo nuevamente.'
                );
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $resultado = DB::transaction(function () use ($request, $id) {
                $usuario = Usuario::lockForUpdate()
                    ->findOrFail($id);

                if (!$usuario->activo) {
                    return 'inactivo';
                }

                if ($request->user()->id_usuario === $usuario->id_usuario) {
                    throw new DomainException(
                        'No puedes desactivar tu propia cuenta.'
                    );
                }

                [$rolAdministrador, $administradoresActivos] =
                    $this->bloquearAdministradoresActivos();

                if (
                    $this->esAdministradorActivo(
                        $usuario,
                        $rolAdministrador
                    )
                    && $administradoresActivos->count() <= 1
                ) {
                    throw new DomainException(
                        'No es posible desactivar al último administrador activo del sistema.'
                    );
                }

                $usuario->activo = false;
                $usuario->save();

                return 'desactivado';
            });

            if ($resultado === 'inactivo') {
                return redirect()
                    ->route('usuarios.index')
                    ->with(
                        'info',
                        'El usuario ya se encuentra desactivado.'
                    );
            }

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'success',
                    'El usuario fue desactivado correctamente.'
                );

        } catch (DomainException $e) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', $e->getMessage());
        } catch (Throwable $e) {

            Log::error(
                'Error al desactivar un usuario.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_usuario' => $id,
                ]
            );

            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No fue posible desactivar el usuario. Inténtalo nuevamente.'
                );
        }
    }

    private function bloquearAdministradoresActivos(): array
    {
        $rolAdministrador = Rol::where('nombre', 'ADMINISTRADOR')
            ->where('activo', true)
            ->lockForUpdate()
            ->first();

        if (!$rolAdministrador) {
            return [null, collect()];
        }

        $administradoresActivos = Usuario::where('id_rol', $rolAdministrador->id_rol)
            ->where('activo', true)
            ->orderBy('id_usuario')
            ->lockForUpdate()
            ->get();

        return [$rolAdministrador, $administradoresActivos];
    }

    private function esAdministradorActivo(
        Usuario $usuario,
        ?Rol $rolAdministrador
    ): bool {
        return $rolAdministrador
            && $usuario->activo
            && $usuario->id_rol === $rolAdministrador->id_rol;
    }
}
