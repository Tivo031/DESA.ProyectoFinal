<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $consulta = Paciente::query();

            if ($request->filled('buscar')) {
                $buscar = trim($request->buscar);

                $consulta->where(function ($query) use ($buscar) {
                    $query->where('nombres', 'like', '%' . $buscar . '%')
                        ->orWhere('apellidos', 'like', '%' . $buscar . '%')
                        ->orWhere('dpi', 'like', '%' . $buscar . '%')
                        ->orWhere('telefono', 'like', '%' . $buscar . '%')
                        ->orWhere('correo', 'like', '%' . $buscar . '%');
                });
            }

            if ($request->filled('estado')) {
                $consulta->where(
                    'activo',
                    $request->estado === 'activo' ? 1 : 0
                );
            }

            $pacientes = $consulta
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->paginate(10)
                ->withQueryString();

            $resumenPacientes = [
                'total' => Paciente::count(),
                'activos' => Paciente::where('activo', true)->count(),
                'inactivos' => Paciente::where('activo', false)->count(),
            ];

            return view(
                'pacientes.index',
                compact('pacientes', 'resumenPacientes')
            );

        } catch (Throwable $e) {
            Log::error('Error al cargar pacientes.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No fue posible cargar los pacientes.'
            );
        }
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'dpi' => [
                'nullable',
                'digits:13',
                'unique:pacientes,dpi',
            ],
            'nombres' => [
                'required',
                'string',
                'max:80',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:80',
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'sexo' => [
                'nullable',
                'in:FEMENINO,MASCULINO,OTRO,NO_INDICA',
            ],
            'telefono' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:120',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:250',
            ],
        ], [
            'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
            'dpi.unique' => 'Ya existe un paciente con este DPI.',
            'nombres.required' => 'Debes ingresar los nombres.',
            'apellidos.required' => 'Debes ingresar los apellidos.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.required' => 'Debes ingresar un número de teléfono.',
            'telefono.min' => 'El teléfono debe tener al menos 8 números.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
        ]);

        try {
            $datos['id_usuario_registro'] = $request->user()->id_usuario;
            $datos['activo'] = true;

            $paciente = Paciente::create($datos);

            return redirect()
                ->route('pacientes.show', $paciente->id_paciente)
                ->with('success', 'Paciente registrado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al registrar paciente.', [
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible registrar el paciente.');
        }
    }

    public function show($id)
    {
        try {
            $paciente = Paciente::with([
                'usuarioRegistro',
                'citas' => function ($query) {
                    $query->with([
                        'servicio',
                        'estado',
                        'especialista.usuario',
                    ])->orderByDesc('inicio');
                },
            ])->findOrFail($id);

            return view('pacientes.show', compact('paciente'));

        } catch (Throwable $e) {
            Log::error('Error al cargar paciente.', [
                'id_paciente' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('error', 'No fue posible cargar el paciente.');
        }
    }

    public function edit($id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            return view('pacientes.edit', compact('paciente'));

        } catch (Throwable $e) {
            Log::error('Error al cargar paciente para edición.', [
                'id_paciente' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('error', 'No fue posible cargar el paciente.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            $datos = $request->validate([
                'dpi' => [
                    'nullable',
                    'digits:13',
                    Rule::unique('pacientes', 'dpi')
                        ->ignore($paciente->id_paciente, 'id_paciente'),
                ],
                'nombres' => [
                    'required',
                    'string',
                    'max:80',
                ],
                'apellidos' => [
                    'required',
                    'string',
                    'max:80',
                ],
                'fecha_nacimiento' => [
                    'nullable',
                    'date',
                    'before_or_equal:today',
                ],
                'sexo' => [
                    'nullable',
                    'in:FEMENINO,MASCULINO,OTRO,NO_INDICA',
                ],
                'telefono' => [
                    'required',
                    'string',
                    'min:8',
                    'max:20',
                    'regex:/^[0-9]+$/',
                ],
                'correo' => [
                    'nullable',
                    'email',
                    'max:120',
                ],
                'direccion' => [
                    'nullable',
                    'string',
                    'max:250',
                ],
            ], [
                'dpi.digits' => 'El DPI debe contener exactamente 13 números.',
                'dpi.unique' => 'Ya existe otro paciente con este DPI.',
                'nombres.required' => 'Debes ingresar los nombres.',
                'apellidos.required' => 'Debes ingresar los apellidos.',
                'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
                'telefono.required' => 'Debes ingresar un número de teléfono.',
                'telefono.min' => 'El teléfono debe tener al menos 8 números.',
                'telefono.regex' => 'El teléfono solo puede contener números.',
                'correo.email' => 'Ingresa un correo electrónico válido.',
            ]);

            $paciente->update($datos);

            return redirect()
                ->route('pacientes.show', $paciente->id_paciente)
                ->with('success', 'Paciente actualizado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al actualizar paciente.', [
                'id_paciente' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No fue posible actualizar el paciente.');
        }
    }

    public function destroy($id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            if (!$paciente->activo) {
                return redirect()
                    ->route('pacientes.index')
                    ->with('error', 'El paciente ya se encuentra inactivo.');
            }

            $paciente->update([
                'activo' => false,
            ]);

            return redirect()
                ->route('pacientes.index')
                ->with('success', 'Paciente desactivado correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al desactivar paciente.', [
                'id_paciente' => $id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'No fue posible desactivar el paciente.');
        }
    }
}