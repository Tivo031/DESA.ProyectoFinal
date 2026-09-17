<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar'));
        $estado = $request->input('estado');

        $roles = Rol::query()
            ->withCount([
                'usuarios',
                'permisos',
            ])
            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {
                    $query->where(function ($q) use ($buscar) {
                        $q->where(
                            'nombre',
                            'like',
                            "%{$buscar}%"
                        )
                            ->orWhere(
                                'descripcion',
                                'like',
                                "%{$buscar}%"
                            );
                    });
                }
            )
            ->when(
                $estado !== null && $estado !== '',
                function ($query) use ($estado) {
                    $query->where(
                        'activo',
                        (bool) $estado
                    );
                }
            )
            ->orderBy('id_rol')
            ->paginate(10)
            ->withQueryString();

        $resumenRoles = [
            'total' => Rol::count(),
            'activos' => Rol::where(
                'activo',
                true
            )->count(),
            'inactivos' => Rol::where(
                'activo',
                false
            )->count(),
        ];

        return view(
            'roles.index',
            compact(
                'roles',
                'resumenRoles'
            )
        );
    }

    public function create(Request $request)
    {
        $permisos = collect();

        if ($request->user()->can('roles.permisos')) {

            $permisos = Permiso::where('activo', true)
                ->orderBy('modulo')
                ->orderBy('nombre')
                ->get()
                ->groupBy('modulo');
        }

        return view(
            'roles.create',
            compact('permisos')
        );
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => strtoupper(
                trim((string) $request->input('nombre'))
            ),
            'descripcion' => trim(
                (string) $request->input('descripcion')
            ),
        ]);

        $puedeAdministrarPermisos =
            $request->user()->can('roles.permisos');

        // REGLAS

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-ZÁÉÍÓÚÑ0-9 _-]+$/u',
                'unique:roles,nombre',
            ],

            'descripcion' => [
                'required',
                'string',
                'max:150',
            ],

            'activo' => [
                'required',
                'boolean',
            ],
        ];

        // PERMISOS

        if ($puedeAdministrarPermisos) {

            $reglas['permisos'] = [
                'nullable',
                'array',
            ];

            $reglas['permisos.*'] = [
                'integer',
                'distinct',
                Rule::exists(
                    'permisos',
                    'id_permiso'
                )->where(
                        'activo',
                        true
                    ),
            ];
        }

        // VALIDACIÓN

        $datos = $request->validate(
            $reglas,
            [
                'nombre.required' =>
                    'El nombre del rol es obligatorio.',

                'nombre.min' =>
                    'El nombre del rol debe tener al menos 3 caracteres.',

                'nombre.max' =>
                    'El nombre del rol no puede superar los 50 caracteres.',

                'nombre.regex' =>
                    'El nombre del rol únicamente puede contener letras, números, espacios, guiones y guiones bajos.',

                'nombre.unique' =>
                    'Ya existe un rol con este nombre.',

                'descripcion.required' =>
                    'La descripción es obligatoria.',

                'descripcion.max' =>
                    'La descripción no puede superar los 150 caracteres.',

                'permisos.array' =>
                    'Los permisos seleccionados no son válidos.',

                'permisos.*.exists' =>
                    'Uno de los permisos seleccionados no es válido.',

                'permisos.*.distinct' =>
                    'Existen permisos duplicados en la selección.',
            ]
        );

        try {

            DB::transaction(
                function () use ($datos, $puedeAdministrarPermisos) {

                    // ROL
    
                    $rol = Rol::create([
                        'nombre' =>
                            $datos['nombre'],

                        'descripcion' =>
                            $datos['descripcion'],

                        'activo' =>
                            $datos['activo'],
                    ]);

                    // PERMISOS
    
                    if ($puedeAdministrarPermisos) {

                        $rol->permisos()->sync(
                            $datos['permisos'] ?? []
                        );
                    }
                }
            );

            return redirect()
                ->route('roles.index')
                ->with(
                    'success',
                    'El rol fue creado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al crear un rol.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'nombre' => $datos['nombre'],
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible crear el rol. Inténtalo nuevamente.'
                );
        }
    }

    public function edit(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        $permisos = collect();

        if ($request->user()->can('roles.permisos')) {

            $rol->load('permisos');

            $permisos = Permiso::where('activo', true)
                ->orderBy('modulo')
                ->orderBy('nombre')
                ->get()
                ->groupBy('modulo');
        }

        return view(
            'roles.edit',
            compact(
                'rol',
                'permisos'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        $request->merge([
            'nombre' => strtoupper(
                trim((string) $request->input('nombre'))
            ),
            'descripcion' => trim(
                (string) $request->input('descripcion')
            ),
        ]);

        $puedeAdministrarPermisos =
            $request->user()->can('roles.permisos');

        $puedeCambiarEstado =
            $request->user()->can('roles.desactivar');


        // REGLAS

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-ZÁÉÍÓÚÑ0-9 _-]+$/u',
                Rule::unique('roles', 'nombre')
                    ->ignore(
                        $rol->id_rol,
                        'id_rol'
                    ),
            ],

            'descripcion' => [
                'required',
                'string',
                'max:150',
            ],
        ];


        // ESTADO

        if ($puedeCambiarEstado) {

            $reglas['activo'] = [
                'required',
                'boolean',
            ];
        }


        // PERMISOS

        if ($puedeAdministrarPermisos) {

            $reglas['permisos'] = [
                'nullable',
                'array',
            ];

            $reglas['permisos.*'] = [
                'integer',
                'distinct',
                Rule::exists(
                    'permisos',
                    'id_permiso'
                )->where(
                        'activo',
                        true
                    ),
            ];
        }


        // VALIDACIÓN

        $datos = $request->validate(
            $reglas,
            [
                'nombre.required' =>
                    'El nombre del rol es obligatorio.',

                'nombre.min' =>
                    'El nombre del rol debe tener al menos 3 caracteres.',

                'nombre.max' =>
                    'El nombre del rol no puede superar los 50 caracteres.',

                'nombre.regex' =>
                    'El nombre del rol únicamente puede contener letras, números, espacios, guiones y guiones bajos.',

                'nombre.unique' =>
                    'Ya existe un rol con este nombre.',

                'descripcion.required' =>
                    'La descripción es obligatoria.',

                'descripcion.max' =>
                    'La descripción no puede superar los 150 caracteres.',

                'permisos.array' =>
                    'Los permisos seleccionados no son válidos.',

                'permisos.*.exists' =>
                    'Uno de los permisos seleccionados no es válido.',

                'permisos.*.distinct' =>
                    'Existen permisos duplicados en la selección.',
            ]
        );


        try {

            DB::transaction(
                function () use ($rol, $datos, $puedeAdministrarPermisos, $puedeCambiarEstado) {

                    // ROL
    
                    $rol->nombre =
                        $datos['nombre'];

                    $rol->descripcion =
                        $datos['descripcion'];


                    // ESTADO
    
                    if ($puedeCambiarEstado) {

                        $rol->activo =
                            (bool) $datos['activo'];
                    }

                    $rol->save();


                    // PERMISOS
    
                    if ($puedeAdministrarPermisos) {

                        $rol->permisos()->sync(
                            $datos['permisos'] ?? []
                        );
                    }
                }
            );

            return redirect()
                ->route('roles.index')
                ->with(
                    'success',
                    'El rol fue actualizado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al actualizar un rol.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_rol' => $rol->id_rol,
                    'nombre' => $datos['nombre'],
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible actualizar el rol. Inténtalo nuevamente.'
                );
        }
    }

    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);

        try {

            // VALIDAR ESTADO

            if (!$rol->activo) {
                return redirect()
                    ->route('roles.index')
                    ->with(
                        'info',
                        'El rol ya se encuentra desactivado.'
                    );
            }

            // VALIDAR USUARIOS ACTIVOS

            $tieneUsuariosActivos = $rol->usuarios()
                ->where('activo', true)
                ->exists();

            if ($tieneUsuariosActivos) {
                return redirect()
                    ->route('roles.index')
                    ->with(
                        'error',
                        'No es posible desactivar el rol porque tiene usuarios activos asignados.'
                    );
            }

            // DESACTIVAR

            $rol->activo = false;
            $rol->save();

            return redirect()
                ->route('roles.index')
                ->with(
                    'success',
                    'El rol fue desactivado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al desactivar un rol.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_rol' => $rol->id_rol,
                    'nombre' => $rol->nombre,
                ]
            );

            return redirect()
                ->route('roles.index')
                ->with(
                    'error',
                    'No fue posible desactivar el rol. Inténtalo nuevamente.'
                );
        }
    }
}