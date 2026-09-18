<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
class PermisoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->input('buscar')
        );

        $modulo = trim(
            (string) $request->input('modulo')
        );

        $estado = $request->input('estado');

        $permisos = Permiso::query()
            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {

                    $query->where(
                        function ($q) use ($buscar) {

                            $q->where(
                                'codigo',
                                'like',
                                "%{$buscar}%"
                            )
                                ->orWhere(
                                    'nombre',
                                    'like',
                                    "%{$buscar}%"
                                )
                                ->orWhere(
                                    'descripcion',
                                    'like',
                                    "%{$buscar}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $modulo !== '',
                function ($query) use ($modulo) {
                    $query->where(
                        'modulo',
                        $modulo
                    );
                }
            )
            ->when(
                $estado !== null &&
                $estado !== '',
                function ($query) use ($estado) {
                    $query->where(
                        'activo',
                        (bool) $estado
                    );
                }
            )
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $modulos = Permiso::query()
            ->select('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        $resumenPermisos = [
            'total' => Permiso::count(),

            'activos' => Permiso::where(
                'activo',
                true
            )->count(),

            'inactivos' => Permiso::where(
                'activo',
                false
            )->count(),
        ];

        return view(
            'permisos.index',
            compact(
                'permisos',
                'modulos',
                'resumenPermisos'
            )
        );
    }

    public function create()
    {
        return view('permisos.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'codigo' => strtolower(
                trim((string) $request->input('codigo'))
            ),

            'nombre' => trim(
                (string) $request->input('nombre')
            ),

            'modulo' => strtoupper(
                trim((string) $request->input('modulo'))
            ),

            'descripcion' => trim(
                (string) $request->input('descripcion')
            ),
        ]);

        // VALIDACIÓN

        $datos = $request->validate(
            [
                'codigo' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[a-z0-9._-]+$/',
                    'unique:permisos,codigo',
                ],

                'nombre' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                ],

                'modulo' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[A-ZÁÉÍÓÚÑ0-9 _-]+$/u',
                ],

                'descripcion' => [
                    'required',
                    'string',
                    'max:200',
                ],

                'activo' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'codigo.required' =>
                    'El código del permiso es obligatorio.',

                'codigo.min' =>
                    'El código debe tener al menos 3 caracteres.',

                'codigo.max' =>
                    'El código no puede superar los 100 caracteres.',

                'codigo.regex' =>
                    'El código únicamente puede contener letras minúsculas, números, puntos, guiones y guiones bajos.',

                'codigo.unique' =>
                    'Ya existe un permiso con este código.',

                'nombre.required' =>
                    'El nombre del permiso es obligatorio.',

                'nombre.min' =>
                    'El nombre del permiso debe tener al menos 3 caracteres.',

                'nombre.max' =>
                    'El nombre del permiso no puede superar los 100 caracteres.',

                'modulo.required' =>
                    'El módulo es obligatorio.',

                'modulo.min' =>
                    'El módulo debe tener al menos 3 caracteres.',

                'modulo.max' =>
                    'El módulo no puede superar los 50 caracteres.',

                'modulo.regex' =>
                    'El módulo únicamente puede contener letras, números, espacios, guiones y guiones bajos.',

                'descripcion.required' =>
                    'La descripción es obligatoria.',

                'descripcion.max' =>
                    'La descripción no puede superar los 200 caracteres.',
            ]
        );

        try {

            Permiso::create([
                'codigo' => $datos['codigo'],
                'nombre' => $datos['nombre'],
                'modulo' => $datos['modulo'],
                'descripcion' => $datos['descripcion'],
                'activo' => $datos['activo'],
            ]);

            return redirect()
                ->route('permisos.index')
                ->with(
                    'success',
                    'El permiso fue creado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al crear un permiso.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'codigo' => $datos['codigo'],
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible crear el permiso. Inténtalo nuevamente.'
                );
        }
    }

    public function edit($id)
    {
        try {

            $permiso = Permiso::findOrFail($id);

            return view(
                'permisos.edit',
                compact('permiso')
            );

        } catch (Throwable $e) {

            Log::error(
                'Error al cargar el permiso.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_permiso' => $id,
                ]
            );

            return redirect()
                ->route('permisos.index')
                ->with(
                    'error',
                    'No fue posible cargar el permiso.'
                );
        }
    }

    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);

        $request->merge([
            'nombre' => trim(
                (string) $request->input('nombre')
            ),

            'modulo' => strtoupper(
                trim((string) $request->input('modulo'))
            ),

            'descripcion' => trim(
                (string) $request->input('descripcion')
            ),
        ]);

        $puedeCambiarEstado =
            $request->user()->can('permisos.desactivar');


        // REGLAS

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'modulo' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-ZÁÉÍÓÚÑ0-9 _-]+$/u',
            ],

            'descripcion' => [
                'required',
                'string',
                'max:200',
            ],
        ];


        // ESTADO

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
                'nombre.required' =>
                    'El nombre del permiso es obligatorio.',

                'nombre.min' =>
                    'El nombre del permiso debe tener al menos 3 caracteres.',

                'nombre.max' =>
                    'El nombre del permiso no puede superar los 100 caracteres.',

                'modulo.required' =>
                    'El módulo es obligatorio.',

                'modulo.min' =>
                    'El módulo debe tener al menos 3 caracteres.',

                'modulo.max' =>
                    'El módulo no puede superar los 50 caracteres.',

                'modulo.regex' =>
                    'El módulo únicamente puede contener letras, números, espacios, guiones y guiones bajos.',

                'descripcion.required' =>
                    'La descripción es obligatoria.',

                'descripcion.max' =>
                    'La descripción no puede superar los 200 caracteres.',
            ]
        );


        try {

            $permiso->nombre =
                $datos['nombre'];

            $permiso->modulo =
                $datos['modulo'];

            $permiso->descripcion =
                $datos['descripcion'];


            // ESTADO

            if ($puedeCambiarEstado) {

                $permiso->activo =
                    (bool) $datos['activo'];
            }

            $permiso->save();


            return redirect()
                ->route('permisos.index')
                ->with(
                    'success',
                    'El permiso fue actualizado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al actualizar un permiso.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_permiso' => $permiso->id_permiso,
                    'codigo' => $permiso->codigo,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible actualizar el permiso. Inténtalo nuevamente.'
                );
        }
    }

    public function destroy($id)
    {
        $permiso = Permiso::findOrFail($id);

        try {

            // VALIDAR ESTADO

            if (!$permiso->activo) {
                return redirect()
                    ->route('permisos.index')
                    ->with(
                        'info',
                        'El permiso ya se encuentra desactivado.'
                    );
            }


            // DESACTIVAR

            $permiso->activo = false;
            $permiso->save();


            return redirect()
                ->route('permisos.index')
                ->with(
                    'success',
                    'El permiso fue desactivado correctamente.'
                );

        } catch (Throwable $e) {

            Log::error(
                'Error al desactivar un permiso.',
                [
                    'mensaje' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                    'id_permiso' => $permiso->id_permiso,
                    'codigo' => $permiso->codigo,
                ]
            );

            return redirect()
                ->route('permisos.index')
                ->with(
                    'error',
                    'No fue posible desactivar el permiso. Inténtalo nuevamente.'
                );
        }
    }
}